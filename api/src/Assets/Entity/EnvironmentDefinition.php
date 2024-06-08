<?php

namespace App\Assets\Entity;

use App\Assets\Repository\EnvironmentDefinitionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EnvironmentDefinitionRepository::class)]
#[ORM\UniqueConstraint(columns:["identifier"])]
class EnvironmentDefinition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['EnvironmentDefinition:read', 'EnvironmentDefinition:write', 'AssetDefinition:read', 'Asset:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['EnvironmentDefinition:read', 'EnvironmentDefinition:write', 'AssetDefinition:read', 'Asset:read', 'EnvironmentDefinition:identifier'])]
    private ?string $identifier = null;

    #[ORM\Column(length: 255)]
    #[Groups(['EnvironmentDefinition:read', 'EnvironmentDefinition:write', 'AssetDefinition:read'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['EnvironmentDefinition:read', 'EnvironmentDefinition:write'])]
    private array $attributes = [];

    #[ORM\OneToMany(mappedBy: 'environmentDefinition', targetEntity: AssetDefinition::class)]
    #[Groups(['EnvironmentDefinition:read', 'EnvironmentDefinition:write'])]
    private Collection $assetDefinitions;

    public function __construct()
    {
        $this->assetDefinitions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @return Collection<int, AssetDefinition>
     */
    public function getAssetDefinitions(): Collection
    {
        return $this->assetDefinitions;
    }

    public function addAssetDefinition(AssetDefinition $assetDefinition): self
    {
        if (!$this->assetDefinitions->contains($assetDefinition)) {
            $this->assetDefinitions->add($assetDefinition);
            $assetDefinition->setEnvironmentDefinition($this);
        }

        return $this;
    }

    public function removeAssetDefinition(AssetDefinition $assetDefinition): self
    {
        if ($this->assetDefinitions->removeElement($assetDefinition)) {
            // set the owning side to null (unless already changed)
            if ($assetDefinition->getEnvironmentDefinition() === $this) {
                $assetDefinition->setEnvironmentDefinition(null);
            }
        }

        return $this;
    }
}
