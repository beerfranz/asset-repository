<?php

namespace App\State;

use App\Entity\Asset;
use App\Entity\AssetDefinition;
use App\Entity\Kind;

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
        $kindRepo = $this->entityManager->getRepository(Kind::class);

        foreach($envs as $environmentName)
        {
            $assetIdentifier = $assetDefinition->getIdentifier() . '-' . $environmentName;

            $asset = $assetRepo->findOneByIdentifier($assetIdentifier);

            if ($asset === null)
            {
                $asset = new Asset();
                $asset->setIdentifier($assetIdentifier);
            }

            $labels = $assetDefinition->getLabels();

            if (isset($labels['kind'])) {
                $kindIdentifier = $labels['kind'];
                $kind = $kindRepo->findOneByIdentifier($kindIdentifier);

                if ($kind === null) {
                    $kind = new Kind();
                    $kind->setIdentifier($kindIdentifier);
                }

                $asset->setKind($kind);
                unset($labels['kind']);
            }

            $labels = array_merge($asset->getLabels(), $labels);
            $labels['environment'] = $environmentName;
            $asset->setLabels($labels);

            $asset->setOwner($assetDefinition->getOwner());
            $asset->setSource($assetDefinition->getSource());

            $asset->setAssetDefinition($assetDefinition);

            $this->entityManager->persist($asset);
            $this->entityManager->flush();
        }
    }
}
