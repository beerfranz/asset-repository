<?php

namespace App\ApiResource;

use App\State\TaskState;
use App\Entity\Task as TaskEntity;

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
    description: 'Task',
    processor: TaskState::class,
    provider: TaskState::class,
    normalizationContext: ['groups' => ['Task:read']],
    denormalizationContext: ['groups' => ['Task:write']],
)]
#[GetCollection(
    security: "is_granted('ASSET_READ')",
    normalizationContext: ['groups' => ['Tasks:read']],
)]
#[Get(
    security: "is_granted('ASSET_READ')",
)]
#[Post(security: "is_granted('ASSET_WRITE')")]
#[Put(security: "is_granted('ASSET_WRITE')")]
class Task
{
    #[Groups(['Tasks:read', 'Task:read'])]
    #[ApiProperty(identifier: true)]
    public $identifier;

    #[Groups(['Tasks:read', 'Task:read', 'Task:write'])]
    public $title;

    #[Groups(['Tasks:read', 'Task:read', 'Task:write'])]
    public $description;

    #[Groups(['Tasks:read', 'Task:read', 'Task:write'])]
    public $isDone;

    #[Groups(['Tasks:read', 'Task:read', 'Task:write'])]
    public $createdAt;

    #[Groups(['Tasks:read', 'Task:read', 'Task:write'])]
    public $assignedTo;

    #[Groups(['Tasks:read', 'Task:read', 'Task:write'])]
    public $taskTemplate;

    #[Groups(['Task:read'])]
    public $events;

    public function populateFromTaskEntity(TaskEntity $task): self
    {
        $this->identifier = $task->getIdentifier();
        $this->title = $task->getTitle();
        $this->description = $task->getDescription();
        $this->createdAt = $task->getCreatedAt();
        $this->isDone = $task->isIsDone();
        $this->assignedTo = $task->getAssignedTo();
        $this->taskTemplate = $task->getTaskTemplate();
        $this->events = $task->getTaskEvents();

        return $this;
    }

    #[ApiProperty(identifier: false)]
    public function getId() {
        return $this->identifier;
    }
}
