<?php

namespace App\Entity;

use App\Repository\TaskWorkflowRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskWorkflowRepository::class)]
#[ORM\UniqueConstraint(columns:["identifier"])]
class TaskWorkflow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $identifier = null;

    #[ORM\Column(type: 'json_document')]
    private array $workflow = [];

    #[ORM\OneToMany(mappedBy: 'taskWorkflow', targetEntity: TaskTemplate::class)]
    private Collection $taskTemplates;

    public function __construct()
    {
        $this->taskTemplates = new ArrayCollection();
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

    public function getWorkflow(): array
    {
        return $this->workflow;
    }

    public function setWorkflow(array $workflow): static
    {
        $this->workflow = $workflow;

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
            $taskTemplate->setTaskWorkflow($this);
        }

        return $this;
    }

    public function removeTaskTemplate(TaskTemplate $taskTemplate): static
    {
        if ($this->taskTemplates->removeElement($taskTemplate)) {
            // set the owning side to null (unless already changed)
            if ($taskTemplate->getTaskWorkflow() === $this) {
                $taskTemplate->setTaskWorkflow(null);
            }
        }

        return $this;
    }
}
