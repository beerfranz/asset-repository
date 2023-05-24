<?php

namespace App\State;

use App\Entity\Asset;
use App\Entity\Owner;
use App\ApiResource\BatchAsset;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RequestStack;


use Psr\Log\LoggerInterface;

final class BatchAssetProcessor extends CommonState implements ProcessorInterface
{
    
    /**
     * @param BatchAssetDto $data
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $repo = $this->entityManager->getRepository(Asset::class);
        $repoOwner = $this->entityManager->getRepository(Owner::class);

        $identifiers = [];

        $user = $this->security->getUser();

        foreach($data->assets as $input)
        {
            $identifier = $input['identifier'];

            $asset = $repo->findOneByIdentifier($identifier);

            if ($asset === null)
            {
                $asset = new Asset();
                $asset->setIdentifier($identifier);
            }
            
            if (isset($input['attributes']))
                $asset->setAttributes($input['attributes']);

            $this->setOwner($asset, $input['owner'] ?? $data->owner);
            $this->setSource($asset, $input['source'] ?? $data->source);
            
            if (isset($input['labels']))
                $asset->setLabels($input['labels']);

            $asset = $this->setVersion($asset, $input['version']);
            
            $identifiers[] = $asset->getIdentifier();

            $this->entityManager->persist($asset);
            $this->entityManager->flush();
        }

        // For PUT request, remove assets not present
        if ($operation instanceof Put && isset($data->source))
        {
            // Get assets
            // $assetsToRemove = $repo->findUserAssetsByIdentifiersNoIn($user->getEmail(), $identifiers);
            $assetsToRemove = $repo->findAssetsByidentifiersNotIn($identifiers, [ 'source' => $data->source ]);
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
    }
}
