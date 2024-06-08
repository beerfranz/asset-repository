<?php

namespace App\Assessments\Entity;

use App\Assessments\Entity\AssessmentSequence;
use App\Assessments\Repository\AssessmentPlanRepository;
use App\Assessments\Doctrine\PlanListener;

use App\Entity\Asset;
use App\Tasks\Entity\Task;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Serializer\Annotation\Groups;

use Beerfranz\RogerBundle\Entity\RogerEntity;
use Beerfranz\RogerBundle\Entity\RogerIdTrait;
use Beerfranz\RogerBundle\Entity\RogerIdentifierTrait;
use Beerfranz\RogerBundle\Entity\RogerTitleTrait;
use Beerfranz\RogerBundle\Doctrine\RogerListener;

#[ORM\Entity(repositoryClass: AssessmentPlanRepository::class)]
#[ORM\EntityListeners([RogerListener::class])]
class AssessmentPlan extends RogerEntity // implements RogerSequence
{
	use RogerIdTrait;

	#[Groups(['AssessmentPlan'])]
	#[ORM\Column(length: 255, unique: true, nullable: false)]
	private ?string $identifier = null;

	public function getIdentifier(): ?string
	{
		return $this->identifier;
	}

	public function setIdentifier(string $identifier): self
	{
		$this->identifier = $identifier;

		return $this;
	}

	use RogerTitleTrait;

	/**
	 * @var Collection<int, Task>
	 */
	#[Groups(['AssessmentPlan'])]
	#[ORM\ManyToMany(targetEntity: Task::class)]
	private Collection $tasks;

	#[Groups(['AssessmentPlan'])]
	#[ORM\ManyToOne(targetEntity: Asset::class)]
	private ?Asset $asset = null;

	public function __construct()
	{
		$this->tasks = new ArrayCollection();
	}

	public function getSequenceClass(): ?string
	{
		return AssessmentSequence::class;
	}

	public function getSequencedProperties(): array
	{
		return [ 'identifier' => [ 'prefix' => 'a-' ], 'title' => [] ];
	}

	public function getMessengerSerializationGroup(): string
	{
		return 'AssessmentPlan';
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
		}

		return $this;
	}

	public function removeTask(Task $task): static
	{
		$this->tasks->removeElement($task);

		return $this;
	}

	public function getAsset(): ?Asset
	{
		return $this->asset;
	}

	public function setAsset(Asset $asset): static
	{
		$this->asset = $asset;

		return $this;
	}
	
}