<?php

namespace App\State;

use App\Entity\Asset;
use App\Entity\Owner;
use App\Entity\Source;
use App\ApiResource\Asset as AssetDto;
use App\ApiResource\AssetBatchDto;

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

final class AssetState extends CommonState implements ProcessorInterface, ProviderInterface
{
    protected $assetRepo;

    public function __construct(
        EntityManagerInterface $entityManager,
        RequestStack $request,
        LoggerInterface $logger,
        Security $security,
    ) {
        parent::__construct($entityManager, $request, $logger, $security);
        $this->assetRepo = $entityManager->getRepository(Asset::class);
        $this->sourceRepo = $entityManager->getRepository(Source::class);
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface)
        {
            return $this->getCollection($this->assetRepo, $context);
        }

        if ($context['input']['class'] === AssetBatchDto::class) {
            $output = new AssetBatchDto();
            return $output;
        }
        
        return $this->assetRepo->findOneByIdentifier(null, $uriVariables['name']);
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
            
                $assetsToRemove = $this->assetRepo->findAssetsByidentifiersNotIn($identifiers, [ 'source' => $source->getId() ]);
                foreach ($assetsToRemove as $asset)
                {
                    // Drop the link between the asset to remove and the audit, without deleting the audit
                    foreach ($asset->getAssetAudits() as $audit) {
                        $audit->setAsset(null);
                        $this->entityManager->persist($audit);
                    }
                    
                    // Delete the asset
                    $this->entityManager->remove($asset);
                    $this->entityManager->flush();
                }
            }
        } else {
            $identifiers[] = $this->processOneAsset($input)->getIdentifier();
        }
    }

    protected function processOneAsset($data): Asset
    {
        $identifier = $data['identifier'];

        $asset = $this->assetRepo->findOneByIdentifier($identifier);

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

        if (isset($data['labels']))
            $asset->setLabels($data['labels']);

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

        $this->entityManager->persist($asset);
        $this->entityManager->flush();

        return $asset;
    }
}
