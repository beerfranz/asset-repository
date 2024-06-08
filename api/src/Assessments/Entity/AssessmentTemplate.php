<?php

namespace App\Assessments\Entity;

use App\Assets\Entity\Asset;
use App\Assessments\ApiResource\GeneratePlan;

use App\Assessments\Repository\AssessmentTemplateRepository;
use Beerfranz\RogerBundle\Entity\RogerEntity;
use Beerfranz\RogerBundle\Entity\RogerTitleTrait;
use Beerfranz\RogerBundle\Entity\RogerIdTrait;
use Beerfranz\RogerBundle\Entity\RogerIdentifierTrait;
use App\Tasks\Entity\TaskTemplate;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AssessmentTemplateRepository::class)]
class AssessmentTemplate extends RogerEntity
{

	use RogerIdTrait;

	#[Groups(['AssessmentTemplate:messenger'])]
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
	 * @var Collection<int, TaskTemplate>
	 */
	#[Groups(['AssessmentTemplate:messenger'])]
	#[ORM\ManyToMany(targetEntity: TaskTemplate::class)]
	private Collection $taskTemplates;

	/**
	 * @var Collection<int, Asset>
	 */
	#[Groups(['AssessmentTemplate:messenger'])]
	#[ORM\ManyToMany(targetEntity: Asset::class)]
	private Collection $assets;

	#[Groups(['AssessmentTemplate:messenger'])]
	#[ORM\Column(nullable: true)]
	private ?array $rules = null;

	public function getMessengerSerializationGroup(): string
	{
		return 'AssessmentTemplate:messenger';
	}

	public function __construct()
	{
		$this->taskTemplates = new ArrayCollection();
		$this->assets = new ArrayCollection();
		$this->identifier = self::generateUuid();
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
		}

		return $this;
	}

	public function removeTaskTemplate(TaskTemplate $taskTemplate): static
	{
		$this->taskTemplates->removeElement($taskTemplate);

		return $this;
	}

	/**
	 * @return Collection<int, Asset>
	 */
	public function getAssets(): Collection
	{
		return $this->assets;
	}

	public function addAsset(Asset $asset): static
	{
		if (!$this->assets->contains($asset)) {
			$this->assets->add($asset);
		}

		return $this;
	}

	public function removeAsset(Asset $asset): static
	{
		$this->assets->removeElement($asset);

		return $this;
	}

	public function getRules(): ?array
	{
		return $this->rules;
	}

	public function setRules(?array $rules): static
	{
		$this->rules = $rules;

		return $this;
	}
}
