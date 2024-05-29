<?php

namespace App\Assets\State;

use App\Entity\Asset;
use App\Entity\AssetDefinition;
use App\Entity\Environment;
use App\Entity\EnvironmentDefinition;
use App\Entity\Kind;
use App\Entity\Source;
use App\Entity\Relation;
use App\Entity\AssetDefinitionRelation;

trait TraitDefinitionPropagate {
    public function updateAssets(AssetDefinition $assetDefinition) {
        $assetRepo = $this->entityManager->getRepository(Asset::class);
        $sourceRepo = $this->entityManager->getRepository(Source::class);
        $relationRepo = $this->entityManager->getRepository(Relation::class);

        $environmentDefinition = $assetDefinition->getEnvironmentDefinition();

        $identifiers = [];
        $relationIds = [];

        $sourceIdentifier = $assetDefinition->getSource()->getName() . '-' . $assetDefinition->getIdentifier();

        $source = $this->getSource($assetDefinition, $sourceRepo);

        $envs = $this->getEnvs($environmentDefinition);

        if ($envs === null) {
            $identifiers[] = $this->processOneAsset($source, $assetDefinition, null, $assetRepo, $relationRepo)->getIdentifier();
        } else {
            foreach($envs as $environment)
            {
                $identifiers[] = $this->processOneAsset($source, $assetDefinition, $environment, $assetRepo, $relationRepo)->getIdentifier();
            }
        }
        
        $assetRepo->deleteBySourceAndidentifiersNotIn($source, $identifiers);
        $relationRepo->deleteBySourceAndIdsNotIn($source, $relationIds);

    }

    public function getAssetIdentifier(AssetDefinition $assetDefinition, ?string $environmentIdentifier)
    {
        if ($environmentIdentifier === null)
            return $assetDefinition->getIdentifier();

        return $assetDefinition->getIdentifier() . '-' . $environmentIdentifier;
    }

    public function getSource(AssetDefinition $assetDefinition, $sourceRepo): Source
    {
        $sourceIdentifier = $assetDefinition->getSource()->getName() . '-' . $assetDefinition->getIdentifier();

        $source = $sourceRepo->findOneByName($sourceIdentifier);
        if (null === $source) {
            $source = new Source();
            $source->setName($sourceIdentifier);

            $this->entityManager->persist($source);
            $this->entityManager->flush();
        }

        return $source;
    }

    public function getEnvs(?EnvironmentDefinition $environmentDefinition): ?Array
    {
        $envs = [];

        if ($environmentDefinition === null)
            return null;

        $environmentRepo = $this->entityManager->getRepository(Environment::class);

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

        return $envs;
    }

    public function processOneRelation(Asset $asset, AssetDefinitionRelation $relationDefinition, AssetDefinition $assetDefinition, $environmentIdentifier, $assetRepo, $relationRepo): ?Relation
    {
        $assetDefinitionFrom = $relationDefinition->getAssetDefinitionFrom();

        // create relation only if environmentDefinition is the same
        if ($assetDefinition->getEnvironmentDefinition() === $assetDefinitionFrom->getEnvironmentDefinition())
        {

            $assetToIdentifier = $this->getAssetIdentifier($assetDefinitionFrom, $environmentIdentifier);
            $assetTo = $assetRepo->findOneByIdentifier($assetToIdentifier);

            if ($assetTo === null)
                return null;

            $relation = $relationRepo->findByUniq($asset, $assetTo, $relationDefinition->getName());

            if ($relation === null)
            {
                $relation = new Relation();
                $relation->setFromAsset($asset);
                $relation->setToAsset($assetTo);
                $relation->setKind($relationDefinition->getName());
            }

            $relation->setSource($asset->getSource());

            $this->entityManager->persist($relation);
            $this->entityManager->flush();

            return $relation;
        }
        return null;
    }

    public function processOneAsset(Source $source, AssetDefinition $assetDefinition, ?Environment $environment, $assetRepo, $relationRepo): Asset
    {
        $kindRepo = $this->entityManager->getRepository(Kind::class);

        if ($environment !== null)
            $environmentIdentifier = $environment->getIdentifier();
        else
            $environmentIdentifier = null;

        $assetIdentifier = $this->getAssetIdentifier($assetDefinition, $environmentIdentifier);
        
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
        }

        $labels = array_merge($asset->getLabels(), $labels);
        if ($environment !== null)
            $labels['environment'] = $environment->getName();
        $labels['kind'] = $kind->getIdentifier();
        $asset->setLabels($labels);

        // Owner
        $asset->setOwner($assetDefinition->getOwner());
        // Source
        $asset->setSource($source);
        // AssetDefinition
        $asset->setAssetDefinition($assetDefinition);
        // Environment
        $asset->setEnvironment($environment);

        $this->entityManager->persist($asset);
        $this->entityManager->flush();

        foreach ($assetDefinition->getRelationsTo() as $relationDefinition)
        {
            $relation = $this->processOneRelation($asset, $relationDefinition, $assetDefinition, $environmentIdentifier, $assetRepo, $relationRepo);
            if ($relation !== null)
                $relationIds[] = $relation->getId();
        }

        return $asset;
    }

}
