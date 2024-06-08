<?php

namespace App\Assets\State;

use App\Assets\Entity\EnvironmentDefinition;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\State\ProviderInterface;

class EnvironmentDefinitionStateProvider extends CommonState implements ProviderInterface
{
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $repo = $this->entityManager->getRepository(EnvironmentDefinition::class);

        if ($operation instanceof CollectionOperationInterface)
        {
            return $this->getCollection($repo, $context);
        }

        $environmentDefinition = $repo->findOneByIdentifier($uriVariables['id']);

        return $environmentDefinition;
    }
}
