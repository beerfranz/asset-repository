<?php

namespace App\Assets\Entity;

use App\Common\Entity\AssetAttributeType;
use App\Risks\Entity\Risk;
use Beerfranz\RogerBundle\Filter\AutocompleteFilter;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Serializer\Filter\GroupFilter;

use App\Assets\Doctrine\AssetListener;
use App\Assets\Repository\AssetRepository;
use App\Validator\IsValidOwner;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: AssetRepository::class)]
#[ORM\UniqueConstraint(columns:["identifier"])]
#[ORM\EntityListeners([AssetListener::class])]
#[ApiResource(
    normalizationContext: ['groups' => ['Asset:read']],
    denormalizationContext: ['groups' => ['Asset:write']],
    security: "is_granted('ASSET_READ')",
    routePrefix: '/entity',
)]
#[GetCollection]
#[Post(security: "is_granted('ASSET_WRITE')")]
#[Get]
#[Put(security: "is_granted('ASSET_WRITE')")]
#[Patch(security: "is_granted('ASSET_WRITE')")]
#[Delete(security: "is_granted('ASSET_WRITE')")]
#[ApiFilter(OrderFilter::class, properties: ['id', 'identifier'])]
#[ApiFilter(AutocompleteFilter::class, properties: [ 'identifier', 'assetDefinition.identifier', 'kind.identifier', 'environment.identifier'])]
#[ApiFilter(GroupFilter::class, arguments: ['parameterName' => 'groups', 'overrideDefaultGroups' => true, 'whitelist' => ['Asset:identifier', 'AssetDefinition:identifier', 'Kind:identifier', 'Asset:environment']])]
#[UniqueEntity(fields: ['identifier'], message: 'There is already an asset this identifier')]
class Asset
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['Asset:read', 'Asset:write'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['Asset:read', 'Asset:write', 'Asset:identifier', 'Instance:read', 'AssessmentPlan'])]
    private ?string $identifier = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['Asset:read', 'Asset:write'])]
    private array $attributes = [];

    #[ORM\Column]
    #[Groups(['Asset:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255)]
    #[Groups(['Asset:read'])]
    private ?string $createdBy = null;

    #[ORM\OneToMany(mappedBy: 'asset', targetEntity: AssetAudit::class)]
    #[Groups(['Asset:read'])]
    private Collection $assetAudits;

    #[Groups(['Asset:read', 'Asset:write'])]
    #[ORM\ManyToOne(inversedBy: 'assets', cascade: ['persist'])]
    private ?Owner $owner = null;

    #[ORM\ManyToOne(inversedBy: 'assets', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['Asset:read'])]
    private ?Source $source = null;

    #[ORM\Column]
    #[Groups(['Asset:read', 'Asset:write'])]
    private array $labels = [];

    #[ORM\OneToMany(mappedBy: 'asset', targetEntity: Instance::class)]
    #[Groups(['Asset:read'])]
    private Collection $instances;

    #[ORM\ManyToOne(inversedBy: 'assets')]
    #[Groups(['Asset:read', 'AssetDefinition:identifier'])]
    private ?AssetDefinition $assetDefinition = null;

    #[ORM\ManyToOne(inversedBy: 'assets')]
    #[Groups(['Asset:read', 'Asset:write'])]
    private ?Version $version = null;

    #[ORM\ManyToOne(inversedBy: 'assets', cascade: ['persist'])]
    #[Groups(['Asset:read', 'Asset:write', 'Asset:kind'])]
    private ?Kind $kind = null;

    #[ORM\ManyToOne(inversedBy: 'assets')]
    #[Groups(['Asset:read', 'Asset:environment'])]
    private ?Environment $environment = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    private ?self $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    private Collection $children;

    #[ORM\OneToMany(mappedBy: 'fromAsset', targetEntity: Relation::class)]
    private Collection $fromRelations;

    #[ORM\OneToMany(mappedBy: 'toAsset', targetEntity: Relation::class)]
    private Collection $toRelations;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['Asset:read', 'Asset:write'])]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['Asset:read', 'Asset:write'])]
    private ?array $links = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['Asset:read', 'Asset:write'])]
    private ?array $rules = null;

    #[ORM\OneToMany(mappedBy: 'asset', targetEntity: Risk::class)]
    private Collection $risks;

    public function __construct()
    {
        $this->assetAudits = new ArrayCollection();
        $this->instances = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->fromRelations = new ArrayCollection();
        $this->toRelations = new ArrayCollection();
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

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(?array $attributes): self
    {
        if ($attributes === null)
            $this->attributes = $attributes;
        elseif (is_array($attributes)) {
            $this->attributes = [];
            foreach($attributes as $namespace => $namespaced_vars) {
                $this->attributes[$namespace] = [];
                if (is_array($namespaced_vars)) {
                    foreach($namespaced_vars as $attributeIdentifier => $attributeProperties) {
                        $attributeObject = new AssetAttributeType($attributeProperties);
                        $this->attributes[$namespace][$attributeIdentifier] = $attributeObject->serialize();
                    }
                }
            }
        } else {
            // fallback if attributes are not well formated
            $this->attributes = $attributes;
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setCreatedBy(string $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * @return Collection<int, AssetAudit>
     */
    public function getAssetAudits(): Collection
    {
        return $this->assetAudits;
    }

    public function addAssetAudit(AssetAudit $assetAudit): self
    {
        if (!$this->assetAudits->contains($assetAudit)) {
            $this->assetAudits->add($assetAudit);
            $assetAudit->setAsset($this);
        }

        return $this;
    }

    public function removeAssetAudit(AssetAudit $assetAudit): self
    {
        if ($this->assetAudits->removeElement($assetAudit)) {
            // set the owning side to null (unless already changed)
            if ($assetAudit->getAsset() === $this) {
                $assetAudit->setAsset(null);
            }
        }

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

    public function getLabels(): array
    {
        return $this->labels;
    }

    public function setLabels(array $labels): self
    {
        $this->labels = $labels;

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
            $instance->setAsset($this);
        }

        return $this;
    }

    public function removeInstance(Instance $instance): self
    {
        if ($this->instances->removeElement($instance)) {
            // set the owning side to null (unless already changed)
            if ($instance->getAsset() === $this) {
                $instance->setAsset(null);
            }
        }

        return $this;
    }

    public function getAssetDefinition(): ?AssetDefinition
    {
        return $this->assetDefinition;
    }

    public function setAssetDefinition(?AssetDefinition $assetDefinition): self
    {
        $this->assetDefinition = $assetDefinition;

        return $this;
    }

    public function getVersion(): ?Version
    {
        return $this->version;
    }

    public function setVersion(?Version $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getKind(): ?Kind
    {
        return $this->kind;
    }

    public function setKind(?Kind $kind): self
    {
        $this->kind = $kind;

        return $this;
    }

    public function getEnvironment(): ?Environment
    {
        return $this->environment;
    }

    public function setEnvironment(?Environment $environment): self
    {
        $this->environment = $environment;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
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

    public function addChild(self $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): self
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Relation>
     */
    public function getFromRelations(): Collection
    {
        return $this->fromRelations;
    }

    public function addFromRelation(Relation $fromRelation): self
    {
        if (!$this->fromRelations->contains($fromRelation)) {
            $this->fromRelations->add($fromRelation);
            $fromRelation->setFromAsset($this);
        }

        return $this;
    }

    public function removeFromRelation(Relation $fromRelation): self
    {
        if ($this->fromRelations->removeElement($fromRelation)) {
            // set the owning side to null (unless already changed)
            if ($fromRelation->getFromAsset() === $this) {
                $fromRelation->setFromAsset(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Relation>
     */
    public function getToRelations(): Collection
    {
        return $this->toRelations;
    }

    public function addToRelation(Relation $toRelation): self
    {
        if (!$this->toRelations->contains($toRelation)) {
            $this->toRelations->add($toRelation);
            $toRelation->setToAsset($this);
        }

        return $this;
    }

    public function removeToRelation(Relation $toRelation): self
    {
        if ($this->toRelations->removeElement($toRelation)) {
            // set the owning side to null (unless already changed)
            if ($toRelation->getToAsset() === $this) {
                $toRelation->setToAsset(null);
            }
        }

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

    public function getLinks(): ?array
    {
        return $this->links;
    }

    public function setLinks(?array $links): static
    {
        $this->links = $links;

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
            $risk->setAsset($this);
        }

        return $this;
    }

    public function removeRisk(Risk $risk): static
    {
        if ($this->risks->removeElement($risk)) {
            // set the owning side to null (unless already changed)
            if ($risk->getAsset() === $this) {
                $risk->setAsset(null);
            }
        }

        return $this;
    }
}
