<?php

namespace App\ApiResource;

use App\State\IndicatorState;
use App\Entity\Indicator as IndicatorEntity;
use App\Entity\Frequency;

use App\ApiResource\TaskTemplate;

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
    description: 'Indicator',
    processor: IndicatorState::class,
    provider: IndicatorState::class,
    normalizationContext: ['groups' => ['Indicator:read']],
    denormalizationContext: ['groups' => ['Indicator:write']],
)]
#[GetCollection(
    security: "is_granted('ASSET_READ')",
    normalizationContext: ['groups' => ['Indicators:read']],
)]
#[Get(
    security: "is_granted('ASSET_READ')",
)]
#[Post(security: "is_granted('ASSET_WRITE')")]
#[Put(security: "is_granted('ASSET_WRITE')")]
#[Delete(security: "is_granted('ASSET_WRITE')")]
class Indicator extends RogerApiResource
{
    #[Groups(['Indicators:read', 'Indicator:read', 'Indicator:write', 'IndicatorValues:read', 'IndicatorValue:read'])]
    #[ApiProperty(identifier: true)]
    public $identifier;

    #[Groups(['Indicators:read', 'Indicator:read', 'Indicator:write'])]
    public $description;

    #[Groups(['Indicators:read', 'Indicator:read', 'Indicator:write'])]
    public $namespace;

    #[Groups(['Indicators:read', 'Indicator:read', 'Indicator:write'])]
    public $targetValue;

    #[Groups(['Indicators:read', 'Indicator:read', 'Indicator:write'])]
    public $triggers;

    #[Groups(['Indicators:read', 'Indicator:read', 'Indicator:write'])]
    #[ApiProperty(
        openapiContext: [ "type" => "object" ]
    )]
    public ?array $frequency = [];

    #[Groups(['Indicators:read', 'Indicator:read', 'Indicator:write'])]
    public $isActivated;

    #[Groups(['Indicators:read', 'Indicator:read'])]
    public $valuesSample = [];

    #[Groups(['Indicators:read', 'Indicator:read', 'Indicator:write'])]
    public ?TaskTemplate $taskTemplate = null;

    public function setValuesSample($samples)
    {
        foreach($samples as $sample) {
            $trigger = $sample->getTrigger();
            $this->valuesSample[] = [
                'identifier' => $sample->getIdentifier(),
                'value' => $sample->getValue(),
                'level' => $trigger['printLevel'],
                'isValidated' => $sample->isIsValidated(),
                'validator' => $sample->getValidator(),
                'taskIdentifier' => $sample->getTaskIdentifier(),
            ];
        }
    }

    public function setTaskTemplate($taskTemplate): self
    {
        if (is_array($taskTemplate))
            $this->taskTemplate = new TaskTemplate($taskTemplate);
        elseif ($taskTemplate instanceof TaskTemplate){
            $this->taskTemplate = $taskTemplate;
        } else {
            $this->taskTemplate = $taskTemplate;
        }

        return $this;
    }
}
