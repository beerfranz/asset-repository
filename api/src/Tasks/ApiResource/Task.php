<?php

namespace App\Tasks\ApiResource;

use App\Tasks\ApiResource\TaskType;
use App\Tasks\ApiResource\TaskTemplate;
use App\Tasks\State\TaskState;

use Beerfranz\RogerBundle\ApiResource\RogerApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;

use Symfony\Component\Serializer\Annotation\Groups;

use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ApiResource(
    description: 'Task',
    processor: TaskState::class,
    provider: TaskState::class,
    normalizationContext: ['groups' => ['Task:read']],
    denormalizationContext: ['groups' => ['Task:write']],
)]
#[GetCollection(
    security: "is_granted('TASK_READ')",
    normalizationContext: ['groups' => ['Tasks:read']],
)]
#[Get(
    security: "is_granted('TASK_READ')",
)]
#[Post(security: "is_granted('TASK_WRITE')")]
#[Put(security: "is_granted('TASK_WRITE')")]
#[Patch(
    security: "is_granted('TASK_WRITE')",
)]
#[Delete(security: "is_granted('TASK_WRITE')")]
class Task extends RogerApiResource
{
    #[Groups(['Tasks:read', 'Task:read', 'Task:write'])]
    #[ApiProperty(identifier: true)]
    public $identifier;

    #[Groups(['Tasks:read', 'Task:read', 'Task:write'])]
    public $title;

    #[Groups(['Tasks:read', 'Task:read', 'Task:write'])]
    public $description;

    #[Groups(['Tasks:read', 'Task:read', 'Task:write'])]
    public $status;

    #[Groups(['Tasks:read', 'Task:read', 'Task:write'])]
    public $isDone;

    #[Groups(['Tasks:read', 'Task:read', 'Task:write'])]
    public $createdAt;

    #[Groups(['Tasks:read', 'Task:read', 'Task:write'])]
    public $owner;

    #[Groups(['Tasks:read', 'Task:read', 'Task:write'])]
    public $assignedTo;

    #[Groups(['Tasks:read', 'Task:read'])]
    public ?TaskTemplate $taskTemplate;

    #[Groups(['Task:write'])]
    public $taskTemplateIdentifier;

    #[Groups(['Task:read', 'Task:write'])]
    public $attributes;

    #[Groups(['Task:read'])]
    public $events;

    #[Groups(['Task:read'])]
    public $allowedNextStatuses;

    #[Groups(['Tasks:read', 'Task:read', 'Task:write'])]
    #[ApiProperty(
        openapiContext: [ "type" => "object" ]
    )]
    public ?array $tags = [];

    #[Groups(['Tasks:read', 'Task:read'])]
    public ?TaskType $taskType;

}
