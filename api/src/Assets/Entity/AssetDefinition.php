<?php

namespace App\Assets\Entity;

use App\Filter\AutocompleteFilter;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Serializer\Filter\GroupFilter;

use App\Assets\Repository\AssetDefinitionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: AssetDefinitionRepository::class)]
#[ORM\UniqueConstraint(columns:["identifier"])]
#[ApiResource(
    normalizationContext: ['groups' => ['AssetDefinition:read']],
    denormalizationContext: ['groups' => ['AssetDefinition:write']],
    security: "is_granted('ASSET_READ')"
)]
#[ApiFilter(OrderFilter::class, properties: ['id', 'identifier'])]
#[ApiFilter(AutocompleteFilter::class, properties: [ 'identifier'])]
#[ApiFilter(GroupFilter::class, arguments: ['parameterName' => 'groups', 'overrideDefaultGroups' => true, 'whitelist' => ['AssetDefinition:identifier']])]
#[UniqueEntity(fields: ['identifier'], message: 'There is already an asset this identifier')]
class AssetDefinition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['AssetDefinition:read', 'EnvironmentDefinition:read', 'Asset:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['AssetDefinition:read', 'AssetDefinition:write', 'EnvironmentDefinition:read', 'Asset:read', 'Version:read', 'AssetDefinition:identifier'])]
    private ?string $identifier = null;

    #[ORM\Column(length: 255)]
    #[Groups(['AssetDefinition:read', 'AssetDefinition:write', 'EnvironmentDefinition:read'])]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'assetDefinition', targetEntity: Asset::class)]
    #[Groups(['AssetDefinition:read', 'AssetDefinition:write'])]
    private Collection $assets;

    #[ORM\Column(nullable: true)]
    #[Groups(['AssetDefinition:read', 'AssetDefinition:write'])]
    private array $tags = [];

    #[ORM\ManyToOne(inversedBy: 'assetDefinitions')]
    #[Groups(['AssetDefinition:read', 'AssetDefinition:write'])]
    private ?EnvironmentDefinition $environmentDefinition = null;

    #[ORM\ManyToOne(inversedBy: 'assetDefinitions', cascade: ['persist'])]
    #[Groups(['AssetDefinition:read', 'AssetDefinition:write'])]
    private ?Owner $owner = null;

    #[ORM\ManyToOne(inversedBy: 'assetDefinitions', cascade: ['persist'])]
    #[Groups(['AssetDefinition:read', 'AssetDefinition:write'])]
    private ?Source $source = null;

    #[ORM\OneToMany(mappedBy: 'assetDefinition', targetEntity: Version::class)]
    private Collection $versions;

    #[ORM\Column]
    #[Groups(['AssetDefinition:read', 'AssetDefinition:write'])]
    private array $labels = [];

    #[ORM\OneToMany(mappedBy: 'assetDefinitionFrom', targetEntity: AssetDefinitionRelation::class, orphanRemoval: true)]
    private Collection $relationsFrom;

    #[ORM\OneToMany(mappedBy: 'assetDefinitionTo', targetEntity: AssetDefinitionRelation::class, orphanRemoval: true)]
    #[Groups(['AssetDefinition:read'])]
    private Collection $relationsTo;

    public function __construct()
    {
        $this->assets = new ArrayCollection();
        $this->versions = new ArrayCollection();
        $this->relationsFrom = new ArrayCollection();
        $this->relationsTo = new ArrayCollection();
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
            $asset->setAssetDefinition($this);
        }

        return $this;
    }

    public function removeAsset(Asset $asset): self
    {
        if ($this->assets->removeElement($asset)) {
            // set the owning side to null (unless already changed)
            if ($asset->getAssetDefinition() === $this) {
                $asset->setAssetDefinition(null);
            }
        }

        return $this;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTags(?array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function getEnvironmentDefinition(): ?EnvironmentDefinition
    {
        return $this->environmentDefinition;
    }

    public function setEnvironmentDefinition(?EnvironmentDefinition $environmentDefinition): self
    {
        $this->environmentDefinition = $environmentDefinition;

        return $this;
    }

    public function getOwner(): ?Owner
    {
        return $this->owner;
    }

    public function setOwner(?Owner $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getSource(): ?Source
    {
        return $this->source;
    }

    public function setSource(?Source $source): self
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @return Collection<int, Version>
     */
    public function getVersions(): Collection
    {
        return $this->versions;
    }

    public function addVersion(Version $version): self
    {
        if (!$this->versions->contains($version)) {
            $this->versions->add($version);
            $version->setAssetDefinition($this);
        }

        return $this;
    }

    public function removeVersion(Version $version): self
    {
        if ($this->versions->removeElement($version)) {
            // set the owning side to null (unless already changed)
            if ($version->getAssetDefinition() === $this) {
                $version->setAssetDefinition(null);
            }
        }

        return $this;
    }

    public function getLabels(): array
    {
        return $this->labels;
    }

    public function setLabels(array $labels): self
    {
        $this->labels = $labels;

        return $this;
    }

    public function addLabel(string $key, string $value): self
    {
        $this->labels[$key] = $value;

        return $this;
    }

    /**
     * @return Collection<int, AssetDefinitionRelation>
     */
    public function getRelationsFrom(): Collection
    {
        return $this->relationsFrom;
    }

    public function addRelationFrom(AssetDefinitionRelation $assetDefinitionRelation): self
    {
        if (!$this->relationsFrom->contains($assetDefinitionRelation)) {
            $this->relationsFrom->add($assetDefinitionRelation);
            $assetDefinitionRelation->setAssetDefinitionFrom($this);
        }

        return $this;
    }

    public function removeRelationFrom(AssetDefinitionRelation $assetDefinitionRelation): self
    {
        if ($this->relationsFrom->removeElement($assetDefinitionRelation)) {
            // set the owning side to null (unless already changed)
            if ($assetDefinitionRelation->getAssetDefinitionFrom() === $this) {
                $assetDefinitionRelation->setAssetDefinitionFrom(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AssetDefinitionRelation>
     */
    public function getRelationsTo(): Collection
    {
        return $this->relationsTo;
    }

    public function addRelationsTo(AssetDefinitionRelation $assetDefinitionRelationsTo): self
    {
        if (!$this->relationsTo->contains($assetDefinitionRelationsTo)) {
            $this->relationsTo->add($assetDefinitionRelationsTo);
            $assetDefinitionRelationsTo->setAssetDefinitionTo($this);
        }

        return $this;
    }

    public function removeRelationsTo(AssetDefinitionRelation $assetDefinitionRelationsTo): self
    {
        if ($this->relationsTo->removeElement($assetDefinitionRelationsTo)) {
            // set the owning side to null (unless already changed)
            if ($assetDefinitionRelationsTo->getAssetDefinitionTo() === $this) {
                $assetDefinitionRelationsTo->setAssetDefinitionTo(null);
            }
        }

        return $this;
    }
}
