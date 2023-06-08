<?php

namespace App\State;

use App\Entity\Asset;
use App\Entity\AssetDefinition;
use App\Entity\Environment;
use App\Entity\EnvironmentDefinition;
use App\Entity\Kind;

trait TraitDefinitionPropagate {
    public function updateAssets(AssetDefinition $assetDefinition) {
        $assetRepo = $this->entityManager->getRepository(Asset::class);
        $kindRepo = $this->entityManager->getRepository(Kind::class);
        $environmentRepo = $this->entityManager->getRepository(Environment::class);

        $envs = [];

        $environmentDefinition = $assetDefinition->getEnvironmentDefinition();

        if ($environmentDefinition === null)
            return true;

        foreach($environmentDefinition->getAttributes() as $envIdentifier => $value)
        {
            if (is_array($value)) {
                foreach($value as $subEnvIdentifier)
                {
                    $subEnvIdentifier = $envIdentifier . '-' . $subEnvIdentifier;
                    $env = $environmentRepo->findOneByIdentifier($subEnvIdentifier);
                    if ($env === null) {
                        $env = new Environment();
                        $env->setIdentifier($subEnvIdentifier);
                        $env->setName(ucfirst($subEnvIdentifier));

                        $this->entityManager->persist($env);
                        $this->entityManager->flush();
                    }
                    $envs[] = $env;
                }
            } else {
                $env = $environmentRepo->findOneByIdentifier($envIdentifier);
                if ($env === null) {
                    $env = new Environment();
                    $env->setIdentifier($envIdentifier);
                    $env->setName(ucfirst($envIdentifier));

                    $this->entityManager->persist($env);
                    $this->entityManager->flush();
                }
                $envs[] = $env;
            }
        }

        
        foreach($envs as $environment)
        {
            $environmentIdentifier = $environment->getIdentifier();

            $assetIdentifier = $assetDefinition->getIdentifier() . '-' . $environmentIdentifier;

            $asset = $assetRepo->findOneByIdentifier($assetIdentifier);

            if ($asset === null)
            {
                $asset = new Asset();
                $asset->setIdentifier($assetIdentifier);
            }

            // labels
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
            $labels['environment'] = $environment->getName();
            $asset->setLabels($labels);

            // Owner
            $asset->setOwner($assetDefinition->getOwner());
            // Source
            $asset->setSource($assetDefinition->getSource());
            // AssetDefinition
            $asset->setAssetDefinition($assetDefinition);
            // Environment
            $asset->setEnvironment($environment);

            $this->entityManager->persist($asset);
            $this->entityManager->flush();
        }
    }

}
