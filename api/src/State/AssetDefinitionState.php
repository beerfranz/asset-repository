<?php

namespace App\State;

use App\Entity\AssetDefinition;
use App\Entity\AssetDefinitionRelation;
use App\Entity\EnvironmentDefinition;
use App\Entity\Version;
use App\Entity\Owner;
use App\ApiResource\Version as VersionDto;
use App\ApiResource\BatchAssetDefinition;

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
    protected $repo;
    protected $repoEnvironmentDefinition;

    use TraitDefinitionPropagate;

    public function __construct(
        EntityManagerInterface $entityManager,
        RequestStack $request,
        LoggerInterface $logger,
        Security $security,
    ) {
        parent::__construct($entityManager, $request, $logger, $security);
        $this->repo = $entityManager->getRepository(AssetDefinition::class);
        $this->repoEnvironmentDefinition = $entityManager->getRepository(EnvironmentDefinition::class);
        $this->repoRelation = $entityManager->getRepository(AssetDefinitionRelation::class);
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return false;
    }
    
    /**
     * @param AssetDefinition $data
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        return false;
    }

    public function processOneAssetDefinition($data)
    {
        $identifier = $data['identifier'];

        $assetDefinition = $this->repo->findOneByIdentifier($identifier);

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
            $environmentDefinition = $this->repoEnvironmentDefinition->findOneByIdentifier($data['environmentDefinition']);
            $assetDefinition->setEnvironmentDefinition($environmentDefinition);
        }

        if (isset($data['tags']))
            $assetDefinition->setTags($data['tags']);

        if (isset($data['labels'])) {
            $assetDefinition->setLabels($data['labels']);
        }

        $this->entityManager->persist($assetDefinition);
        $this->entityManager->flush();

        return $assetDefinition;
    }

    public function processOneAssetDefinitionRelation(AssetDefinition $assetDefinitionRelationFrom, AssetDefinition $assetDefinitionRelationTo, $data)
    {
        $assetDefinitionRelation = $this->repoRelation->findOneByIdentifier($assetDefinitionRelationFrom, $assetDefinitionRelationTo);

        if ($assetDefinitionRelation === null)
        {
            $assetDefinitionRelation = new AssetDefinitionRelation();
            $assetDefinitionRelation->setAssetDefinitionFrom($assetDefinitionRelationFrom);
            $assetDefinitionRelation->setAssetDefinitionTo($assetDefinitionRelationTo);
        }

        $assetDefinitionRelation->setName($data['relation']);

        $this->entityManager->persist($assetDefinitionRelation);
        $this->entityManager->flush();
    }
}
