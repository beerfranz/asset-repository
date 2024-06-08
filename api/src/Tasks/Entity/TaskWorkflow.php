<?php

namespace App\Tasks\Entity;

use App\Tasks\Entity\TaskType;

use App\Tasks\Repository\TaskWorkflowRepository;

use Beerfranz\RogerBundle\Entity\RogerEntity;

use Symfony\Component\Serializer\Annotation\Groups;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskWorkflowRepository::class)]
#[ORM\UniqueConstraint(columns:["identifier"])]
class TaskWorkflow extends RogerEntity
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column(length: 255)]
	#[Groups(['Task'])]
	private ?string $identifier = null;

	#[ORM\Column(type: 'json_document')]
	#[Groups(['Task'])]
	private array $workflow = [];

	#[ORM\OneToMany(mappedBy: 'taskWorkflow', targetEntity: TaskType::class)]
	private Collection $taskTypes;

	public function __construct()
	{
		$this->taskTypes = new ArrayCollection();
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
	 * @return Collection<int, TaskType>
	 */
	public function getTaskTypes(): Collection
	{
		return $this->taskTypes;
	}

	public function addTaskType(TaskType $taskType): static
	{
		if (!$this->taskTypes->contains($taskType)) {
			$this->taskTypes->add($taskType);
			$taskType->setTaskWorkflow($this);
		}

		return $this;
	}

	public function removeTaskType(TaskType $taskType): static
	{
		if ($this->taskTypes->removeElement($taskType)) {
			// set the owning side to null (unless already changed)
			if ($taskType->getTaskWorkflow() === $this) {
				$taskType->setTaskWorkflow(null);
			}
		}

		return $this;
	}
}
