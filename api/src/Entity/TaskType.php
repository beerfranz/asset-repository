<?php

namespace App\Entity;

use App\Repository\TaskTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskTypeRepository::class)]
class TaskType extends RogerEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $identifier = null;

    #[ORM\ManyToOne(inversedBy: 'taskTypes')]
    private ?TaskWorkflow $taskWorkflow = null;

    #[ORM\OneToMany(mappedBy: 'taskType', targetEntity: TaskTemplate::class)]
    private Collection $taskTemplates;

    #[ORM\OneToMany(mappedBy: 'taskType', targetEntity: Task::class)]
    private Collection $tasks;

    public function __construct()
    {
        $this->taskTemplates = new ArrayCollection();
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

    public function getTaskWorkflow(): ?TaskWorkflow
    {
        return $this->taskWorkflow;
    }

    public function setTaskWorkflow(?TaskWorkflow $taskWorkflow): static
    {
        $this->taskWorkflow = $taskWorkflow;

        return $this;
    }

    /**
     * @return Collection<int, TaskTemplate>
     */
    public function getTaskTemplates(): Collection
    {
        return $this->taskTemplates;
    }

    public function addTaskTemplate(TaskTemplate $taskTemplate): static
    {
        if (!$this->taskTemplates->contains($taskTemplate)) {
            $this->taskTemplates->add($taskTemplate);
            $taskTemplate->setTaskType($this);
        }

        return $this;
    }

    public function removeTaskTemplate(TaskTemplate $taskTemplate): static
    {
        if ($this->taskTemplates->removeElement($taskTemplate)) {
            // set the owning side to null (unless already changed)
            if ($taskTemplate->getTaskType() === $this) {
                $taskTemplate->setTaskType(null);
            }
        }

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
            $task->setTaskType($this);
        }

        return $this;
    }

    public function removeTask(Task $task): static
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getTaskType() === $this) {
                $task->setTaskType(null);
            }
        }

        return $this;
    }
}
