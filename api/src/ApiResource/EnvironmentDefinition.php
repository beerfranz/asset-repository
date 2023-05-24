<?php

namespace App\ApiResource;

use App\State\EnvironmentDefinitionStateProvider;
use App\State\EnvironmentDefinitionProcessor;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ApiResource(
    description: 'Environment definitions',
    processor: EnvironmentDefinitionProcessor::class,
    provider: EnvironmentDefinitionStateProvider::class,
    normalizationContext: ['groups' => ['EnvironmentDefinition:read']],
    denormalizationContext: ['groups' => ['EnvironmentDefinition:write']],
)]
#[GetCollection(security: "is_granted('ASSET_READ')")]
#[Get(security: "is_granted('ASSET_READ')")]
#[Post(security: "is_granted('ASSET_WRITE')")]
#[Put(security: "is_granted('ASSET_WRITE')")]
class EnvironmentDefinition
{
    #[Groups(['EnvironmentDefinition:read'])]
    public $id;

    #[Groups(['EnvironmentDefinition:read', 'EnvironmentDefinition:write', 'AssetDefinition:read'])]
    #[ApiProperty(identifier: true)]
    public $identifier;

    #[Groups(['EnvironmentDefinition:read', 'EnvironmentDefinition:write'])]
    public $name;

    #[Groups(['EnvironmentDefinition:read', 'EnvironmentDefinition:write'])]
    public $attributes;

    #[Groups(['EnvironmentDefinition:read'])]
    public array $assetDefinitions = [];

    public function getId() {
        return $this->identifier;
    }

}
