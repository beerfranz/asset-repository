<?php

namespace App\ApiResource;

use App\State\IndicatorValueState;
use App\Entity\IndicatorValue as IndicatorValueEntity;

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
    description: 'IndicatorValue',
    processor: IndicatorValueState::class,
    provider: IndicatorValueState::class,
    normalizationContext: ['groups' => ['IndicatorValue:read']],
    denormalizationContext: ['groups' => ['IndicatorValue:write']],
)]
#[GetCollection(
    security: "is_granted('ASSET_READ')",
    normalizationContext: ['groups' => ['IndicatorValues:read']],
)]
#[Get(security: "is_granted('ASSET_READ')",)]
##[Post(security: "is_granted('ASSET_WRITE')")]
#[Post(uriTemplate: '/indicators/{indicatorIdentifier}/values', uriVariables: [ 'indicatorIdentifier'] ,
    security: "is_granted('ASSET_WRITE')")]
#[Put(uriTemplate: '/indicators/{indicatorIdentifier}/values/{identifier}', uriVariables: [ 'indicatorIdentifier', 'identifier' ], security: "is_granted('ASSET_WRITE')")]
class IndicatorValue
{
    #[Groups(['IndicatorValues:read', 'IndicatorValue:read', 'IndicatorValue:write'])]
    #[ApiProperty(identifier: true)]
    public $identifier;

    #[Groups(['IndicatorValues:read', 'IndicatorValue:read', 'IndicatorValue:write'])]
    public \DateTimeImmutable $datetime;

    #[Groups(['IndicatorValues:read', 'IndicatorValue:read', 'IndicatorValue:write'])]
    public Indicator $indicator;

    #[Groups(['IndicatorValues:read', 'IndicatorValue:read', 'IndicatorValue:write'])]
    public $value;

    #[Groups(['IndicatorValues:read', 'IndicatorValue:read', 'IndicatorValue:write'])]
    public $isValidated;

    #[Groups(['IndicatorValues:read', 'IndicatorValue:read', 'IndicatorValue:write'])]
    public $validator;

    public function populateFromIndicatorValueEntity(IndicatorValueEntity $indicatorValue): self
    {
        $this->identifier = $indicatorValue->getIdentifier();
        // $this->datetime = $indicatorValue->getDatetime();
        // $this->indicator = $indicatorValue->getIndicator();
        $this->value = $indicatorValue->getValue();
        // $this->isValidated = $indicatorValue->isIsValidated();
        // $this->validator = $indicatorValue->getValidator();

        return $this;
    }

    #[ApiProperty(identifier: false)]
    public function getId() {
        return $this->identifier;
    }
}
