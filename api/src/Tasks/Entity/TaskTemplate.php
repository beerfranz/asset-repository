<?php

namespace App\Tasks\Entity;

use App\Tasks\Repository\TaskTemplateRepository;

use App\Tasks\Entity\Task;
use App\Tasks\Entity\TaskType;
use App\Tasks\Entity\TaskTag;

use Beerfranz\RogerBundle\Entity\RogerEntity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: TaskTemplateRepository::class)]
#[ORM\UniqueConstraint(columns:["identifier"])]
class TaskTemplate extends RogerEntity
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column(length: 255)]
	#[Groups(['Task'])]
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

	#[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children', cascade: ['persist'])]
	#[MaxDepth(1)]
	private ?self $parent = null;

	#[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
	private Collection $children;

	#[ORM\ManyToOne(inversedBy: 'taskTemplates')]
	#[Groups(['Task'])]
	private ?TaskType $taskType = null;

	#[ORM\Column(nullable: true)]
	private ?array $attributes = null;

	/**
	 * @var Collection<int, TaskTag>
	 */
	#[ORM\ManyToMany(targetEntity: TaskTag::class, inversedBy: 'taskTemplates', cascade: ['persist'])]
	private Collection $tags;

	public function __construct()
	{
		$this->tasks = new ArrayCollection();
		$this->children = new ArrayCollection();
		$this->tags = new ArrayCollection();
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

	public function getParent(): ?self
	{
		return $this->parent;
	}

	public function setParent(?self $parent): static
	{
		$this->parent = $parent;

		return $this;
	}

	/**
	 * @return Collection<int, self>
	 */
	public function getChildren(): Collection
	{
		return $this->children;
	}

	public function addChild(self $child): static
	{
		if (!$this->children->contains($child)) {
			$this->children->add($child);
			$child->setParent($this);
		}

		return $this;
	}

	public function removeChild(self $child): static
	{
		if ($this->children->removeElement($child)) {
			// set the owning side to null (unless already changed)
			if ($child->getParent() === $this) {
				$child->setParent(null);
			}
		}

		return $this;
	}

	public function getTaskType(): ?TaskType
	{
		return $this->taskType;
	}

	public function setTaskType(?TaskType $taskType): static
	{
		$this->taskType = $taskType;

		return $this;
	}

	public function getAttributes(): ?array
	{
		return $this->attributes;
	}

	public function setAttributes(?array $attributes): static
	{
		$this->attributes = $attributes;

		return $this;
	}

	/**
	 * @return Collection<int, TaskTag>
	 */
	public function getTags(): Collection
	{
		return $this->tags;
	}

	public function addTag(TaskTag $tag): static
	{
		if (!$this->tags->contains($tag)) {
			$this->tags->add($tag);
		}

		return $this;
	}

	public function removeTag(TaskTag $tag): static
	{
		$this->tags->removeElement($tag);

		return $this;
	}
}
