<?php

namespace App\Entity;

use App\Repository\IndicatorRepository;

use Beerfranz\RogerBundle\Entity\RogerEntity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IndicatorRepository::class)]
#[ORM\UniqueConstraint(columns:["identifier"])]
class Indicator extends RogerEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $identifier = null;

    #[ORM\Column(length: 255)]
    private ?string $namespace = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?int $targetValue = null;

    #[ORM\Column(nullable: true, type: 'json_document')]
    private ?array $triggers = null;

    #[ORM\Column(nullable: true, type: 'json_document')]
    private ?array $frequency = null;

    #[ORM\Column]
    private ?bool $isActivated = true;

    #[ORM\OneToMany(mappedBy: 'indicator', targetEntity: IndicatorValue::class)]
    private Collection $indicatorValues;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $taskTemplateIdentifier = null;

    public function __construct()
    {
        $this->indicatorValues = new ArrayCollection();
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

    public function getNamespace(): ?string
    {
        return $this->namespace;
    }

    public function setNamespace(string $namespace): static
    {
        $this->namespace = $namespace;

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

    public function getTargetValue(): ?int
    {
        return $this->targetValue;
    }

    public function setTargetValue(?int $targetValue): static
    {
        $this->targetValue = $targetValue;

        return $this;
    }

    public function getTriggers(): ?array
    {
        return $this->triggers;
    }

    public function setTriggers(?array $triggers): static
    {
        $this->triggers = $triggers;

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

    public function isIsActivated(): ?bool
    {
        return $this->isActivated;
    }

    public function setIsActivated(?bool $isActivated): static
    {
        if ($isActivated === null)
            $isActivated = true;
        
        $this->isActivated = $isActivated;

        return $this;
    }

    /**
     * @return Collection<int, IndicatorValue>
     */
    public function getIndicatorValues(): Collection
    {
        return $this->indicatorValues;
    }

    public function addIndicatorValue(IndicatorValue $indicatorValue): static
    {
        if (!$this->indicatorValues->contains($indicatorValue)) {
            $this->indicatorValues->add($indicatorValue);
            $indicatorValue->setIndicator($this);
        }

        return $this;
    }

    public function removeIndicatorValue(IndicatorValue $indicatorValue): static
    {
        if ($this->indicatorValues->removeElement($indicatorValue)) {
            // set the owning side to null (unless already changed)
            if ($indicatorValue->getIndicator() === $this) {
                $indicatorValue->setIndicator(null);
            }
        }

        return $this;
    }

    public function getTaskTemplateIdentifier(): ?string
    {
        return $this->taskTemplateIdentifier;
    }

    public function setTaskTemplateIdentifier(?string $taskTemplateIdentifier): static
    {
        $this->taskTemplateIdentifier = $taskTemplateIdentifier;

        return $this;
    }
}
