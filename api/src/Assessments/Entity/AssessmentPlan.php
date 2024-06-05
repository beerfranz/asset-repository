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

use Beerfranz\RogerBundle\Entity\RogerEntity;
use Beerfranz\RogerBundle\Entity\RogerIdTrait;
use Beerfranz\RogerBundle\Entity\RogerIdentifierTrait;
use Beerfranz\RogerBundle\Entity\RogerTitleTrait;

#[ORM\Entity(repositoryClass: AssessmentPlanRepository::class)]
#[ORM\EntityListeners([PlanListener::class])]
class AssessmentPlan extends RogerEntity // implements RogerSequence
{
	use RogerIdTrait;

	use RogerIdentifierTrait;

	use RogerTitleTrait;

	/**
	 * @var Collection<int, Task>
	 */
	#[ORM\ManyToMany(targetEntity: Task::class)]
	private Collection $tasks;

	#[ORM\ManyToOne(targetEntity: Asset::class)]
	private ?Asset $asset = null;

	public function __construct()
	{
		$this->tasks = new ArrayCollection();
	}

	public function getSequenceClass() {
		return AssessmentSequence::class;
	}

	public function getSequencedProperties() {
		return [ 'identifier' => [ 'prefix' => 'a-' ], 'title' => [] ];
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