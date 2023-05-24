<?php

namespace App\State;

use App\Entity\AssetDefinition;
use App\Entity\Owner;
use App\Entity\Source;
use App\Entity\EnvironmentDefinition;

use App\ApiResource\BatchAssetDefinition;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RequestStack;


use Psr\Log\LoggerInterface;

final class BatchAssetDefinitionProcessor extends CommonState implements ProcessorInterface
{
    use TraitDefinitionPropagate;

    /**
     * @param BatchAssetDefinitionDto $data
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $repo = $this->entityManager->getRepository(AssetDefinition::class);
        $repoOwner = $this->entityManager->getRepository(Owner::class);
        $repoSource = $this->entityManager->getRepository(Source::class);

        $source = $repoSource->findOneByName($data->source);

        $identifiers = [];

        $user = $this->security->getUser();

        foreach($data->assetDefinitions as $input)
        {
            $identifier = $input['identifier'];

            $assetDefinition = $repo->findOneByIdentifier($identifier);

            if ($assetDefinition === null)
            {
                $assetDefinition = new AssetDefinition();
                $assetDefinition->setIdentifier($identifier);
            }

            if (isset($input['name']))
                $assetDefinition->setName($input['name']);

            $this->setOwner($assetDefinition, $input['owner'] ?? $data->owner);
            $this->setSource($assetDefinition, $input['source'] ?? $data->source);

            if (isset($input['environmentDefinition'])) {
                $repoEnvironmentDefinition = $this->entityManager->getRepository(EnvironmentDefinition::class);
                $environmentDefinition = $repoEnvironmentDefinition->findOneByIdentifier($input['environmentDefinition']);
                $assetDefinition->setEnvironmentDefinition($environmentDefinition);
            }

            if (isset($input['tags']))
                $assetDefinition->setTags($input['tags']);

            if (isset($input['labels'])) {
                $assetDefinition->setLabels($input['labels']);
            }
            
            $identifiers[] = $assetDefinition->getIdentifier();

            $this->entityManager->persist($assetDefinition);
            $this->entityManager->flush();

            $this->updateAssets($assetDefinition);
        }

        // For PUT request, remove assets not present
        if ($operation instanceof Put && $data->source !== null)
        {
            // Get assets
            $assetDefinitionsToRemove = $repo->findAssetDefinitionsByidentifiersNotIn($identifiers, [ 'source' => $source->getId() ] );
            foreach ($assetDefinitionsToRemove as $assetDefinition)
            {
                foreach ($assetDefinition->getAssets() as $asset) {
                    $asset->setAssetDefinition(null);
                    $this->entityManager->persist($asset);
                }

                // Delete the asset
                $this->entityManager->remove($assetDefinition);
            }
            $this->entityManager->flush();
        }

        // Generate assets
        
    }
}
