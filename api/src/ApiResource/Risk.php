<?php

namespace App\ApiResource;

use App\State\RiskState;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;

use Symfony\Component\Serializer\Annotation\Groups;

use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ApiResource(
    description: 'Risk',
    processor: RiskState::class,
    provider: RiskState::class,
    normalizationContext: ['groups' => ['Risk:read']],
    denormalizationContext: ['groups' => ['Risk:write']],
)]
#[GetCollection(
    security: "is_granted('ASSET_READ')",
    normalizationContext: ['groups' => ['Risks:read']],
)]
#[Get(
    security: "is_granted('ASSET_READ')",
)]
#[Post(security: "is_granted('ASSET_WRITE')")]
#[Put(security: "is_granted('ASSET_WRITE')")]
class Risk
{
    #[Groups(['Risks:read', 'Risk:read'])]
    #[ApiProperty(identifier: false)]
    public $id;

    #[Groups(['Risks:read', 'Risk:read', 'Risk:write'])]
    #[ApiProperty(identifier: true)]
    public $identifier;

    #[Groups(['Risks:read', 'Risk:read', 'Risk:write'])]
    public $asset;

    #[Groups(['Risks:read', 'Risk:read', 'Risk:write'])]
    public $riskManager;

    #[Groups(['Risks:read', 'Risk:read', 'Risk:write'])]
    public $values;

    #[Groups(['Risks:read', 'Risk:read', 'Risk:write'])]
    public $mitigations;

    public function getId() {
        return $this->identifier;
    }
}
