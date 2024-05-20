<?php

namespace App\Risks\ApiResource;

use App\Risks\State\RiskManagerState;

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
	description: 'RiskManager',
	processor: RiskManagerState::class,
	provider: RiskManagerState::class,
	normalizationContext: ['groups' => ['RiskManager:read']],
	denormalizationContext: ['groups' => ['RiskManager:write']],
)]
#[GetCollection(
	security: "is_granted('ASSET_READ')",
	normalizationContext: ['groups' => ['RiskManagers:read']],
)]
#[Get(
	security: "is_granted('ASSET_READ')",
)]
#[Post(security: "is_granted('ASSET_WRITE')")]
#[Put(security: "is_granted('ASSET_WRITE')")]
#[Delete(security: "is_granted('ASSET_WRITE')")]
class RiskManager extends RogerApiResource
{
	#[Groups(['RiskManagers:read', 'RiskManager:read', 'RiskManager:write'])]
	#[ApiProperty(identifier: true)]
	public $identifier;

	#[Groups(['RiskManagers:read', 'RiskManager:read', 'RiskManager:write'])]
	public $values;

	#[Groups(['RiskManagers:read', 'RiskManager:read', 'RiskManager:write'])]
	public $valuesAggregator;

	#[Groups(['RiskManagers:read', 'RiskManager:read', 'RiskManager:write'])]
	public $triggers;
}
