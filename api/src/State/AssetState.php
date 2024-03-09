<?php

namespace App\State;

use App\Entity\Asset;
use App\Entity\Instance;
use App\Entity\Owner;
use App\Entity\Source;
use App\ApiResource\Asset as AssetDto;
use App\ApiResource\AssetBatchDto;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\State\ProviderInterface;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RequestStack;

use Psr\Log\LoggerInterface;

final class AssetState extends CommonState implements ProcessorInterface, ProviderInterface
{
    protected $assetRepo;
    protected $instanceRepo;
    protected $sourceRepo;

    public function __construct(
        EntityManagerInterface $entityManager,
        RequestStack $request,
        LoggerInterface $logger,
        Security $security,
    ) {
        parent::__construct($entityManager, $request, $logger, $security);
        $this->assetRepo = $entityManager->getRepository(Asset::class);
        $this->sourceRepo = $entityManager->getRepository(Source::class);
        $this->instanceRepo = $entityManager->getRepository(Instance::class);
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface)
        {
            return $this->getCollection($this->assetRepo, $context);
        }

        if (isset($context['input']['class']) && $context['input']['class'] === AssetBatchDto::class) {
            $output = new AssetBatchDto();
            return $output;
        }

        return $this->assetRepo->find($uriVariables['id']);
    }
    
    /**
     * @param $data
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {

        $user = $this->security->getUser();

        if ($data instanceof AssetBatchDto)
        {
            $identifiers = [];

            foreach($data->getAssets() as $input)
            {
                if (isset($uriVariables['sourceId'])) {
                    if ($input['source'] !== null && $input['source'] !== $uriVariables['sourceId'])
                        throw new \Exception('Invalid source "' . $input['source'] . '" for the asset "'. $input['identifier'] . '". The source must be omitted or same as the URL path "' . $uriVariables['sourceId'] . '"');
                    $input['source'] = $uriVariables['sourceId'];
                }

                $identifiers[] = $this->processOneAsset($input)->getIdentifier();
            }
            
            if ($operation instanceof Put && $uriVariables['sourceId'] !== null)
            {
                $source = $this->sourceRepo->findOneByName($uriVariables['sourceId']);

                // $this->assetRepo->deleteBySourceAndidentifiersNotIn($source, $identifiers);
                $assetToRemove = $this->assetRepo->findAssetsByidentifiersNotIn($identifiers, [ 'source' => $source ]);
                foreach ($assetToRemove as $asset) {
                    $this->delete($asset);
                }
            }
        } else {
            if ($operation instanceof Delete) {
                $this->delete($data);
            } else {
                if (isset($uriVariables['id']))
                $data->id = $uriVariables['id'];

                $asset = $this->processOneAsset((array) $data);

                $data->id = $asset->getId();
            }
            
        }
    }

    protected function processOneAsset($data): Asset
    {
        if (isset($data['identifier'])) {
            $identifier = $data['identifier'];

            $asset = $this->assetRepo->findOneByIdentifier($identifier);
        } else {
            $asset = $this->assetRepo->find($data['id']);
            if ($asset === null)
                throw new NotFoundHttpException('Sorry not existing!');
        }
        
        if ($asset === null)
        {
            $asset = new Asset();
            $asset->setIdentifier($identifier);
        }

        // if (!isset($data['name']))
        //     $data['name'] = ucfirst($data['identifier']);

        // $asset->setName($data['name']);
        
        // Attributes
        if (isset($data['attributes']))
            $asset->setAttributes($data['attributes']);

        // Owner
        if (isset($data['owner'])) {
            if (isset($data['owner']['identifier'])) {
                $this->setOwner($asset, $data['owner']['identifier']);
            } else {
                $this->setOwner($asset, $data['owner']);
            }
        }

        // Source
        $this->setSource($asset, $data['source']);

        if (isset($data['labels'])){
            $asset->setLabels($data['labels']);
        }

        // Kind
        if (isset($data['kind'])) {
            if (isset($data['kind']['identifier'])) {
                $this->setKindByIdentifier($asset, $data['kind']['identifier']);
            }
            else {
                $this->setKindByIdentifier($asset, $data['kind']);
            }
            
        }

        // Version
        if (isset($data['version']))
            $asset = $this->setVersion($asset, $data['version']);

        // Description
        if (isset($data['description']))
            $asset->setDescription($data['description']);

        // Links
        if (isset($data['links']))
            $asset->setLinks($data['links']);

        // Rules
        if (isset($data['rules']))
            $asset->setRules($data['rules']);

        $this->entityManager->persist($asset);
        $this->entityManager->flush();
        $this->entityManager->clear();

        return $asset;
    }

    protected function delete(Asset $asset) {
        foreach ($asset->getAssetAudits() as $audit) {
            $audit->setAsset(null);
            $this->entityManager->persist($audit);
        }
        $this->entityManager->remove($asset);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }
}
