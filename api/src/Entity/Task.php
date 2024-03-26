<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ORM\UniqueConstraint(columns:["identifier"])]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $isDone = false;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $assignedTo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $owner = null;

    #[ORM\OneToMany(mappedBy: 'task', targetEntity: TaskEvent::class)]
    private Collection $taskEvents;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    private ?TaskTemplate $taskTemplate = null;

    #[ORM\Column(length: 255)]
    private ?string $identifier = null;

    public function __construct()
    {
        $this->taskEvents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function isIsDone(): ?bool
    {
        return $this->isDone;
    }

    public function setIsDone(bool $isDone): static
    {
        $this->isDone = $isDone;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAssignedTo(): ?string
    {
        return $this->assignedTo;
    }

    public function setAssignedTo(?string $assignedTo): static
    {
        $this->assignedTo = $assignedTo;

        return $this;
    }

    public function getOwner(): ?string
    {
        return $this->owner;
    }

    public function setOwner(?string $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, TaskEvent>
     */
    public function getTaskEvents(): Collection
    {
        return $this->taskEvents;
    }

    public function addTaskEvent(TaskEvent $taskEvent): static
    {
        if (!$this->taskEvents->contains($taskEvent)) {
            $this->taskEvents->add($taskEvent);
            $taskEvent->setTask($this);
        }

        return $this;
    }

    public function removeTaskEvent(TaskEvent $taskEvent): static
    {
        if ($this->taskEvents->removeElement($taskEvent)) {
            // set the owning side to null (unless already changed)
            if ($taskEvent->getTask() === $this) {
                $taskEvent->setTask(null);
            }
        }

        return $this;
    }

    public function getTaskTemplate(): ?TaskTemplate
    {
        return $this->taskTemplate;
    }

    public function setTaskTemplate(?TaskTemplate $taskTemplate): static
    {
        $this->taskTemplate = $taskTemplate;

        return $this;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): static
    {
        $this->identifier = $identifier;

        return $this;
    }
}