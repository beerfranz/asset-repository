<?php

namespace App\Tasks\Entity;

use App\Tasks\Entity\TaskTemplate;
use App\Tasks\Entity\TaskType;
use App\Tasks\Entity\TaskTag;
use App\Message\TaskMessage;

use App\Tasks\Repository\TaskRepository;
use Beerfranz\RogerBundle\Doctrine\RogerListener;

use Beerfranz\RogerBundle\Entity\RogerEntity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ORM\UniqueConstraint(columns:["identifier"])]
#[ORM\Index(name: "isDone_idx", fields: ["isDone"])]
#[ORM\EntityListeners([RogerListener::class])]
class Task extends RogerEntity
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	#[Groups(['Task'])]
	private ?int $id = null;

	#[ORM\Column(length: 255)]
	#[Groups(['Task'])]
	private ?string $title = null;

	#[ORM\Column(type: Types::TEXT, nullable: true)]
	#[Groups(['Task'])]
	private ?string $description = null;

	#[ORM\Column]
	#[Groups(['Task'])]
	private ?bool $isDone = false;

	#[ORM\Column]
	#[Groups(['Task'])]
	private ?\DateTimeImmutable $createdAt = null;

	#[ORM\Column(length: 255, nullable: true)]
	#[Groups(['Task'])]
	private ?string $assignedTo = null;

	#[ORM\Column(length: 255, nullable: true)]
	#[Groups(['Task'])]
	private ?string $owner = null;

	#[ORM\ManyToOne(inversedBy: 'tasks', cascade: ['persist'])]
	#[Groups(['Task'])]
	private ?TaskTemplate $taskTemplate = null;

	#[ORM\Column(length: 255)]
	#[Groups(['Task', 'AssessmentPlan'])]
	private ?string $identifier = null;

	#[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
	#[MaxDepth(1)]
	#[Groups(['Task'])]
	private ?self $parent = null;

	#[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
	private Collection $children;

	#[ORM\Column(nullable: true, length: 255)]
	#[Groups(['Task'])]
	private ?string $status = null;

	#[ORM\ManyToOne(inversedBy: 'tasks', cascade: ['persist'])]
	#[Groups(['Task'])]
	private ?TaskType $taskType = null;

	#[ORM\Column(nullable: true)]
	#[Groups(['Task'])]
	private ?array $attributes = null;

	public function __construct()
	{
		$this->children = new ArrayCollection();
		$this->tags = new ArrayCollection();
	}

	public function getMessengerSerializationGroup(): ?string
	{
		return 'Task';
	}

	public function getMessengerClass(): string
	{
		return TaskMessage::class;
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

	public function getStatus(): ?string
	{
		return $this->status;
	}

	public function setStatus(string $status): static
	{
		$this->status = $status;

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
	 * @var Collection<int, TaskTag>
	 */
	#[ORM\ManyToMany(targetEntity: TaskTag::class, inversedBy: 'tasks')]
	private Collection $tags;

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
