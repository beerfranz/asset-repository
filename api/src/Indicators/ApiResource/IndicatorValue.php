<?php

namespace App\Indicators\ApiResource;

use App\Indicators\ApiResource\Indicator;
use App\Indicators\State\IndicatorValueState;
use App\Indicators\Entity\IndicatorValue as IndicatorValueEntity;

use Beerfranz\RogerBundle\ApiResource\RogerApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;

use Symfony\Component\Serializer\Annotation\Groups;

use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ApiResource(
	description: 'IndicatorValue',
	processor: IndicatorValueState::class,
	provider: IndicatorValueState::class,
	normalizationContext: ['groups' => ['IndicatorValue:read']],
	denormalizationContext: ['groups' => ['IndicatorValue:write']],
)]
#[GetCollection(
	uriTemplate: '/indicators/{indicatorIdentifier}/values',
	uriVariables: [ 'indicatorIdentifier'] ,
	security: "is_granted('INDICATOR_READ')",
	normalizationContext: ['groups' => ['IndicatorValues:read']],
)]
#[Get(
	uriTemplate: '/indicators/{indicatorIdentifier}/values/{identifier}',
	uriVariables: [ 'indicatorIdentifier', 'identifier'],
	security: "is_granted('INDICATOR_READ')"
)]
#[Post(
	uriTemplate: '/indicators/{indicatorIdentifier}/values', 
	uriVariables: [ 'indicatorIdentifier'] ,
	security: "is_granted('INDICATOR_WRITE')"
)]
#[Put(
	uriTemplate: '/indicators/{indicatorIdentifier}/values/{identifier}',
	uriVariables: [ 'indicatorIdentifier', 'identifier' ],
	security: "is_granted('INDICATOR_WRITE')"
)]
#[Patch(
	uriTemplate: '/indicators/{indicatorIdentifier}/values/{identifier}',
	uriVariables: [ 'indicatorIdentifier', 'identifier' ],
	security: "is_granted('INDICATOR_WRITE')"
)]
class IndicatorValue extends RogerApiResource
{
	#[Groups(['IndicatorValues:read', 'IndicatorValue:read', 'IndicatorValue:write'])]
	#[ApiProperty(identifier: true)]
	public $identifier;

	#[Groups(['IndicatorValues:read', 'IndicatorValue:read', 'IndicatorValue:write'])]
	public \DateTimeImmutable $datetime;

	#[Groups(['IndicatorValues:read', 'IndicatorValue:read'])]
	public Indicator $indicator;

	#[Groups(['IndicatorValues:read', 'IndicatorValue:read', 'IndicatorValue:write'])]
	public $value;

	#[Groups(['IndicatorValues:read', 'IndicatorValue:read', 'IndicatorValue:write'])]
	public $isValidated;

	#[Groups(['IndicatorValues:read', 'IndicatorValue:read'])]
	public $validator;
	
	#[Groups(['IndicatorValues:read', 'IndicatorValue:read'])]
	public ?array $trigger = null;

	public function __construct(){
		$this->indicator = new Indicator();
	}

	public function setDatetime($datetime): self
	{
		if (is_string($datetime))
			$this->datetime = new \DateTimeImmutable($datetime);

		if (is_array($datetime))
			$this->datetime = new \DateTimeImmutable($datetime['date'], $datetime['timezone']);

		return $this;
	}

	public function setIndicator($indicator): self
	{
		if (is_array($indicator)) {
			$this->indicator = new Indicator($indicator);
		}

		return $this;
	}

	public function getIndicatorIdentifier() {
		return $this->indicator->identifier;
	}
}
