<?php

namespace App\ApiResource;

use App\State\EnvironmentState;
use App\Filter\AutocompleteFilter;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Serializer\Filter\GroupFilter;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ApiResource(
    description: 'Environment',
    processor: EnvironmentState::class,
    provider: EnvironmentState::class,
    normalizationContext: ['groups' => ['Environment:read']],
    denormalizationContext: ['groups' => ['Environment:write']],
)]
#[GetCollection(security: "is_granted('ASSET_READ')")]
#[Get(security: "is_granted('ASSET_READ')")]
#[Post(security: "is_granted('ASSET_WRITE')")]
#[Put(security: "is_granted('ASSET_WRITE')")]
#[ApiFilter(AutocompleteFilter::class, properties: [ 'identifier'])]
class Environment
{
    #[Groups(['Environment:read'])]
    public $id;

    #[Groups(['Environment:read', 'Environment:write', 'AssetDefinition:read'])]
    #[ApiProperty(identifier: true)]
    public $identifier;

    #[Groups(['Environment:read', 'Environment:write'])]
    public $name;

    #[Groups(['Environment:read'])]
    public array $assets = [];

    public function getId() {
        return $this->identifier;
    }
}
