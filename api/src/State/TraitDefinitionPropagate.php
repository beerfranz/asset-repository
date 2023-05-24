<?php

namespace App\State;

use App\Entity\Asset;
use App\Entity\AssetDefinition;

trait TraitDefinitionPropagate {
    public function updateAssets(AssetDefinition $assetDefinition) {
        $envs = [];

        $environmentDefinition = $assetDefinition->getEnvironmentDefinition();

        if ($environmentDefinition === null)
            return true;

        foreach($environmentDefinition->getAttributes() as $env => $value)
        {
            if (is_array($value)) {
                foreach($value as $subEnv)
                {
                    $envs[] = $env . '-' . $subEnv;
                }
            } else {
                $envs[] = $env;
            }
        }

        $assetRepo = $this->entityManager->getRepository(Asset::class);

        foreach($envs as $environmentName)
        {
            $assetIdentifier = $assetDefinition->getIdentifier() . '-' . $environmentName;

            $asset = $assetRepo->findOneByIdentifier($assetIdentifier);

            if ($asset === null)
            {
                $asset = new Asset();
                $asset->setIdentifier($assetIdentifier);
            }

            $labels = array_merge($asset->getLabels(), $assetDefinition->getLabels());
            $labels['environment'] = $environmentName;
            $asset->setLabels($labels);

            $asset->setOwner($assetDefinition->getOwner());
            $asset->setSource($assetDefinition->getSource());

            $asset->setAssetDefinition($assetDefinition);

            $this->entityManager->persist($asset);

        }

        $this->entityManager->flush();
    }
}
