<?php

namespace App\ApiResource;

use App\State\TaskTemplateState;
use App\Entity\TaskTemplate as TaskTemplateEntity;

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
    description: 'Task template',
    processor: TaskTemplateState::class,
    provider: TaskTemplateState::class,
    normalizationContext: ['groups' => ['TaskTemplate:read']],
    denormalizationContext: ['groups' => ['TaskTemplate:write']],
)]
#[GetCollection(
    security: "is_granted('ASSET_READ')",
    normalizationContext: ['groups' => ['TaskTemplates:read']],
)]
#[Get(
    security: "is_granted('ASSET_READ')",
)]
#[Post(security: "is_granted('ASSET_WRITE')")]
#[Put(security: "is_granted('ASSET_WRITE')")]
class TaskTemplate extends RogerApiResource
{
    #[Groups(['TaskTemplates:read', 'TaskTemplate:read', 'TaskTemplate:write'])]
    #[ApiProperty(identifier: true)]
    public $identifier;

    #[Groups(['TaskTemplates:read', 'TaskTemplate:read', 'TaskTemplate:write'])]
    public $title;

    #[Groups(['TaskTemplates:read', 'TaskTemplate:read', 'TaskTemplate:write'])]
    public $description;

    #[Groups(['TaskTemplates:read', 'TaskTemplate:read', 'TaskTemplate:write'])]
    #[ApiProperty(
        openapiContext: [ "type" => "object" ]
    )]
    public ?array $frequency = [];

    #[Groups(['TaskTemplates:read', 'TaskTemplate:read'])]
    #[ApiProperty(
        openapiContext: [ "type" => "object" ]
    )]
    public ?array $workflow = [];

    #[Groups(['TaskTemplates:read', 'TaskTemplate:read', 'TaskTemplate:write'])]
    public $workflow_identifier;

    public function setTaskWorkflow($taskWorkflow) {
        if (isset($taskWorkflow['identifier']))
            $this->workflow_identifier = $taskWorkflow['identifier'];

        if (isset($taskWorkflow['workflow']))
            $this->workflow = $taskWorkflow['workflow'];
        return $this;
    }
}
