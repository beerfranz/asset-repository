<?php

namespace App\Entity;

use App\Repository\RiskManagerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: RiskManagerRepository::class)]
#[ORM\UniqueConstraint(columns:["identifier"])]
#[UniqueEntity(fields: ['identifier'], message: 'There is already a risk manager with this identifier')]
class RiskManager extends RogerEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['RiskManagers:read', 'RiskManager:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['RiskManagers:read', 'RiskManagers:write', 'RiskManager:read', 'RiskManager:write'])]
    private ?string $identifier = null;

    #[ORM\Column]
    #[Groups(['RiskManagers:read', 'RiskManagers:write', 'RiskManager:read', 'RiskManager:write'])]
    private array $values = [];

    #[ORM\Column(length: 255)]
    #[Groups(['RiskManagers:read', 'RiskManagers:write', 'RiskManager:read', 'RiskManager:write'])]
    private ?string $valuesAggregator = null;

    #[ORM\Column]
    #[Groups(['RiskManagers:read', 'RiskManagers:write', 'RiskManager:read', 'RiskManager:write'])]
    private array $triggers = [];

    #[ORM\OneToMany(mappedBy: 'riskManager', targetEntity: Risk::class)]
    #[Groups(['RiskManagers:read', 'RiskManagers:write', 'RiskManager:read', 'RiskManager:write'])]
    private Collection $risks;

    public function __construct()
    {
        $this->risks = new ArrayCollection();
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

    public function getValues(): array
    {
        return $this->values;
    }

    public function setValues(array $values): static
    {
        $this->values = $values;

        return $this;
    }

    public function getValuesAggregator(): ?string
    {
        return $this->valuesAggregator;
    }

    public function setValuesAggregator(string $valuesAggregator): static
    {
        $this->valuesAggregator = $valuesAggregator;

        return $this;
    }

    public function getTriggers(): array
    {
        return $this->triggers;
    }

    public function setTriggers(array $triggers): static
    {
        $this->triggers = $triggers;

        return $this;
    }

    /**
     * @return Collection<int, Risk>
     */
    public function getRisks(): Collection
    {
        return $this->risks;
    }

    public function addRisk(Risk $risk): static
    {
        if (!$this->risks->contains($risk)) {
            $this->risks->add($risk);
            $risk->setRiskManager($this);
        }

        return $this;
    }

    public function removeRisk(Risk $risk): static
    {
        if ($this->risks->removeElement($risk)) {
            // set the owning side to null (unless already changed)
            if ($risk->getRiskManager() === $this) {
                $risk->setRiskManager(null);
            }
        }

        return $this;
    }
}
