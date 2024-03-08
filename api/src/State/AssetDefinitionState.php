<?php

namespace App\State;

use App\Entity\AssetDefinition;
use App\Entity\AssetDefinitionRelation;
use App\Entity\EnvironmentDefinition;
use App\Entity\Source;
use App\Entity\Owner;
use App\ApiResource\Version as VersionDto;
use App\ApiResource\AssetDefinitionBatchDto;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\State\ProviderInterface;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RequestStack;


use Psr\Log\LoggerInterface;

final class AssetDefinitionState extends CommonState implements ProcessorInterface, ProviderInterface
{
    protected $assetDefinitionRepo;
    protected $environmentDefinitionRepo;
    protected $relationRepo;
    protected $sourceRepo;

    use TraitDefinitionPropagate;

    public function __construct(
        EntityManagerInterface $entityManager,
        RequestStack $request,
        LoggerInterface $logger,
        Security $security,
    ) {
        parent::__construct($entityManager, $request, $logger, $security);
        $this->assetDefinitionRepo = $entityManager->getRepository(AssetDefinition::class);
        $this->environmentDefinitionRepo = $entityManager->getRepository(EnvironmentDefinition::class);
        $this->relationRepo = $entityManager->getRepository(AssetDefinitionRelation::class);
        $this->sourceRepo = $entityManager->getRepository(Source::class);
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface)
        {
            return $this->getCollection($this->assetRepo, $context);
        }

        if ($context['input']['class'] === AssetDefinitionBatchDto::class) {
            $output = new AssetDefinitionBatchDto();
            return $output;
        }
        
        return $this->assetRepo->findOneByIdentifier(null, $uriVariables['name']);
    }
    
    /**
     * @param AssetDefinition $data
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        
        if ($data instanceof AssetDefinitionBatchDto)
        {
            $identifiers = [];
            $relations = [];

            foreach($data->getAssetDefinitions() as $input)
            {
                if (isset($uriVariables['sourceId'])) {
                    if ($input['source'] !== null && $input['source'] !== $uriVariables['sourceId'])
                        throw new \Exception('Invalid source "' . $input['source'] . '" for the asset definition "'. $input['identifier'] . '". The source must be omitted or same as the URL path "' . $uriVariables['sourceId'] . '"');
                    $input['source'] = $uriVariables['sourceId'];
                }

                $assetDefinition = $this->processOneAssetDefinition($input);
                $identifiers[] = $assetDefinition->getIdentifier();

                if (isset($input['relations']))
                {
                    foreach($input['relations'] as $relation)
                    {
                        if (isset($relation['identifier']) && isset($relation['relation'])) {
                            $assetDefinitionRelationTo = $this->assetDefinitionRepo->findOneByIdentifier($relation['identifier']);

                            // Add only minimal data
                            if ($assetDefinitionRelationTo === null) {
                                throw new \Exception('Cannot create a relation between ' . $assetDefinition->getIdentifier() . ' and ' . $relation['identifier'] . '. No AssetDefinition with the identifier ' . $relation['identifier'] . '. You must create the AssetDefinition before.');
                            }

                            if (!isset($relation['source']))
                                $relation['source'] = $input['source'];

                            $relations[] = $this->processOneAssetDefinitionRelation($assetDefinition, $assetDefinitionRelationTo, $relation)->getId();
                        }
                    }
                }

                $this->updateAssets($assetDefinition);

            }
            
            if ($operation instanceof Put && $uriVariables['sourceId'] !== null)
            {
                $source = $this->sourceRepo->findOneByName($uriVariables['sourceId']);

                $assetDefinitionsToRemove = $this->assetDefinitionRepo->findAssetDefinitionsByidentifiersNotIn($identifiers, [ 'source' => $source->getId() ] );
                foreach ($assetDefinitionsToRemove as $assetDefinition)
                {
                    foreach ($assetDefinition->getAssets() as $asset) {
                        $asset->setAssetDefinition(null);
                        $this->entityManager->persist($asset);
                    }

                    foreach ($assetDefinition->getVersions() as $version) {
                        $version->setAssetDefinition(null);
                        $this->entityManager->persist($version);
                    }

                    // Delete the asset
                    $this->entityManager->remove($assetDefinition);
                }
                $this->entityManager->flush();


                $relationsToRemove = $this->relationRepo->findRelationByIdsNotIn($relations, [ 'source' => $source->getId() ] );
                foreach ($relationsToRemove as $relation)
                {
                    $this->entityManager->remove($relation);
                }
                $this->entityManager->flush();
            
            }
        } else {
            $identifiers[] = $this->processOneAsset($input)->getIdentifier();
        }

    }

    public function processOneAssetDefinition($data)
    {
        $identifier = $data['identifier'];

        $assetDefinition = $this->assetDefinitionRepo->findOneByIdentifier($identifier);

        if ($assetDefinition === null)
        {
            $assetDefinition = new AssetDefinition();
            $assetDefinition->setIdentifier($identifier);
        }

        if (!isset($data['name']))
            $data['name'] = ucfirst($data['identifier']);

        $assetDefinition->setName($data['name']);

        $this->setOwner($assetDefinition, $data['owner']);
        $this->setSource($assetDefinition, $data['source']);

        if (isset($data['environmentDefinition'])) {
            $environmentDefinition = $this->environmentDefinitionRepo->findOneByIdentifier($data['environmentDefinition']);
            $assetDefinition->setEnvironmentDefinition($environmentDefinition);
        }

        if (isset($data['tags']))
            $assetDefinition->setTags($data['tags']);

        if (isset($data['labels'])) {
            $assetDefinition->setLabels($data['labels']);
        }
        if (isset($data['kind']))
            $assetDefinition->addLabel('kind', $data['kind']['identifier']);

        $this->entityManager->persist($assetDefinition);
        $this->entityManager->flush();

        return $assetDefinition;
    }

    public function processOneAssetDefinitionRelation(AssetDefinition $assetDefinitionRelationFrom, AssetDefinition $assetDefinitionRelationTo, $data): AssetDefinitionRelation
    {
        $assetDefinitionRelation = $this->relationRepo->findOneByIdentifier($assetDefinitionRelationFrom, $assetDefinitionRelationTo);

        if ($assetDefinitionRelation === null)
        {
            $assetDefinitionRelation = new AssetDefinitionRelation();
            $assetDefinitionRelation->setAssetDefinitionFrom($assetDefinitionRelationFrom);
            $assetDefinitionRelation->setAssetDefinitionTo($assetDefinitionRelationTo);
        }

        $assetDefinitionRelation->setName($data['relation']);
        $this->setSource($assetDefinitionRelation, $data['source']);

        $this->entityManager->persist($assetDefinitionRelation);
        $this->entityManager->flush();

        return $assetDefinitionRelation;
    }
}
