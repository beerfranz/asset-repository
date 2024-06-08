<?php

namespace App\Assets\Entity;

use App\Assets\Repository\SourceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SourceRepository::class)]
#[ORM\UniqueConstraint(columns:["name"])]
class Source
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['Asset:read', 'AssetDefinition:read', 'Source:read', 'Instance:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['Asset:read', 'AssetDefinition:read', 'Source:read', 'Instance:read', 'Source:name'])]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'source', targetEntity: Asset::class, orphanRemoval: true)]
    #[Groups(['Source:read'])]
    private Collection $assets;

    #[ORM\OneToMany(mappedBy: 'source', targetEntity: AssetDefinition::class, orphanRemoval: true)]
    #[Groups(['Source:read'])]
    private Collection $assetDefinitions;

    #[ORM\OneToMany(mappedBy: 'source', targetEntity: Instance::class)]
    #[Groups(['Source:read'])]
    private Collection $instances;

    #[ORM\OneToMany(mappedBy: 'source', targetEntity: Relation::class)]
    private Collection $relations;

    public function __construct()
    {
        $this->assets = new ArrayCollection();
        $this->assetDefinitions = new ArrayCollection();
        $this->instances = new ArrayCollection();
        $this->relations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection<int, Asset>
     */
    public function getAssets(): Collection
    {
        return $this->assets;
    }

    public function addAsset(Asset $asset): self
    {
        if (!$this->assets->contains($asset)) {
            $this->assets->add($asset);
            $asset->setSource($this);
        }

        return $this;
    }

    public function removeAsset(Asset $asset): self
    {
        if ($this->assets->removeElement($asset)) {
            // set the owning side to null (unless already changed)
            if ($asset->getSource() === $this) {
                $asset->setSource(null);
            }
        }

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
            $assetDefinition->setSource($this);
        }

        return $this;
    }

    public function removeAssetDefinition(AssetDefinition $assetDefinition): self
    {
        if ($this->assetDefinitions->removeElement($assetDefinition)) {
            // set the owning side to null (unless already changed)
            if ($assetDefinition->getSource() === $this) {
                $assetDefinition->setSource(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Instance>
     */
    public function getInstances(): Collection
    {
        return $this->instances;
    }

    public function addInstance(Instance $instance): self
    {
        if (!$this->instances->contains($instance)) {
            $this->instances->add($instance);
            $instance->setSource($this);
        }

        return $this;
    }

    public function removeInstance(Instance $instance): self
    {
        if ($this->instances->removeElement($instance)) {
            // set the owning side to null (unless already changed)
            if ($instance->getSource() === $this) {
                $instance->setSource(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Relation>
     */
    public function getRelations(): Collection
    {
        return $this->relations;
    }

    public function addRelation(Relation $relation): self
    {
        if (!$this->relations->contains($relation)) {
            $this->relations->add($relation);
            $relation->setSource($this);
        }

        return $this;
    }

    public function removeRelation(Relation $relation): self
    {
        if ($this->relations->removeElement($relation)) {
            // set the owning side to null (unless already changed)
            if ($relation->getSource() === $this) {
                $relation->setSource(null);
            }
        }

        return $this;
    }
}
