<?php

namespace App\Entity;

use App\Filter\AutocompleteFilter;

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

use App\Doctrine\AssetListener;
use App\Repository\AssetRepository;
use App\Validator\IsValidOwner;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    security: "is_granted('ASSET_READ')"
)]
#[GetCollection]
#[Post(security: "is_granted('ASSET_WRITE')")]
#[Get]
#[Put(security: "is_granted('ASSET_WRITE')")]
#[Patch(security: "is_granted('ASSET_WRITE')")]
#[Delete(security: "is_granted('ASSET_WRITE')")]
#[ApiFilter(OrderFilter::class, properties: ['id', 'identifier'])]
#[ApiFilter(AutocompleteFilter::class, properties: [ 'identifier'])]
#[UniqueEntity(fields: ['identifier'], message: 'There is already an asset this identifier')]
class Asset
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['Asset:read', 'Asset:write'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['Asset:read', 'Asset:write', 'Asset:identifier'])]
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

    #[ORM\ManyToOne(inversedBy: 'assets', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['Asset:read'])]
    private ?Source $source = null;

    #[ORM\Column]
    #[Groups(['Asset:read', 'Asset:write'])]
    private array $labels = [];

    #[ORM\OneToMany(mappedBy: 'asset', targetEntity: Instance::class)]
    #[Groups(['Asset:read'])]
    private Collection $instances;

    #[ORM\ManyToOne(inversedBy: 'assets', cascade: ['persist', 'remove'])]
    #[Groups(['Asset:read'])]
    private ?AssetDefinition $assetDefinition = null;

    #[ORM\ManyToOne(inversedBy: 'assets')]
    #[Groups(['Asset:read', 'Asset:write'])]
    private ?Version $version = null;

    public function __construct()
    {
        $this->assetAudits = new ArrayCollection();
        $this->instances = new ArrayCollection();
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

    public function getType(): ?AssetType
    {
        return $this->type;
    }

    public function setType(?AssetType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(?array $attributes): self
    {
        $this->attributes = $attributes;

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
}
