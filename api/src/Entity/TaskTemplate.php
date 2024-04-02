<?php

namespace App\Entity;

use App\Repository\TaskTemplateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskTemplateRepository::class)]
#[ORM\UniqueConstraint(columns:["identifier"])]
class TaskTemplate extends RogerEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $identifier = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $generateTaskAutomatically = null;

    #[ORM\OneToMany(mappedBy: 'taskTemplate', targetEntity: Task::class)]
    private Collection $tasks;

    #[ORM\Column(nullable: true, type: 'json_document')]
    private ?array $frequency = null;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
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

    public function getGenerateTaskAutomatically(): ?string
    {
        return $this->generateTaskAutomatically;
    }

    public function setGenerateTaskAutomatically(?string $generateTaskAutomatically): static
    {
        $this->generateTaskAutomatically = $generateTaskAutomatically;

        return $this;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): static
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setTaskTemplate($this);
        }

        return $this;
    }

    public function removeTask(Task $task): static
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getTaskTemplate() === $this) {
                $task->setTaskTemplate(null);
            }
        }

        return $this;
    }

    public function getFrequency(): ?array
    {
        return $this->frequency;
    }

    public function setFrequency(?array $frequency): static
    {
        $this->frequency = $frequency;

        return $this;
    }
}
