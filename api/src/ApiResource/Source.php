<?php

namespace App\ApiResource;

use App\State\SourceState;

use App\Filter\AutocompleteFilter;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Serializer\Filter\GroupFilter;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ApiResource(
    description: 'Sources',
    provider: SourceState::class,
    normalizationContext: ['groups' => ['Source:read']],
    denormalizationContext: ['groups' => ['Source:write']],
)]
#[GetCollection(security: "is_granted('ASSET_READ')")]
#[Get(security: "is_granted('ASSET_READ')")]
#[ApiFilter(GroupFilter::class, arguments: ['parameterName' => 'groups', 'overrideDefaultGroups' => true, 'whitelist' => ['Source:name']])]
class Source
{
    #[Groups(['Source:read'])]
    public $id;

    #[Groups(['Source:read', 'AssetDefinition:read', 'Source:name'])]
    #[ApiProperty(identifier: true)]
    #[ApiFilter(AutocompleteFilter::class)]
    public $name;

    #[Groups(['Source:read'])]
    public array $assetDefinitions = [];

    #[Groups(['Source:read'])]
    public array $assets = [];

    #[Groups(['Source:read'])]
    public array $instances = [];

    public function getId() {
        return $this->name;
    }
}
