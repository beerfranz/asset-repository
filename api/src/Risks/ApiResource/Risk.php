<?php

namespace App\Risks\ApiResource;

use App\Risks\State\RiskState;

use Beerfranz\RogerBundle\ApiResource\RogerApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;

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
	security: "is_granted('RISK_READ')",
	normalizationContext: ['groups' => ['Risks:read']],
)]
#[Get(
	security: "is_granted('RISK_READ')",
)]
#[Post(security: "is_granted('RISK_WRITE')")]
#[Put(security: "is_granted('RISK_WRITE')")]
#[Delete(security: "is_granted('RISK_WRITE')")]
class Risk extends RogerApiResource
{
	#[Groups(['Risks:read', 'Risk:read', 'Risk:write'])]
	#[ApiProperty(identifier: true)]
	public $identifier;

	#[Groups(['Risks:read', 'Risk:read', 'Risk:write'])]
	public $asset;

	#[Groups(['Risks:read', 'Risk:read', 'Risk:write'])]
	public $riskManager;

	#[Groups(['Risks:read', 'Risk:read', 'Risk:write'])]
	public $description;

	#[Groups(['Risks:read', 'Risk:read', 'Risk:write'])]
	public $values;

	#[Groups(['Risks:read', 'Risk:read', 'Risk:write'])]
	public $mitigations;
}
