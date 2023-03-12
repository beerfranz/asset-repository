<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
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
#[UniqueEntity(fields: ['identifier'], message: 'There is already an asset this identifier')]
class Asset
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['Asset:read', 'Asset:write'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['Asset:read', 'Asset:write'])]
    private ?string $identifier = null;

    #[ORM\ManyToOne(inversedBy: 'assets')]
    #[Groups(['Asset:read', 'Asset:write'])]
    private ?AssetType $type = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['Asset:read', 'Asset:write'])]
    private array $attributes = [];

    #[ORM\Column(length: 255, nullable: true)]
    #[IsValidOwner()]
    #[Groups(['Asset:read', 'Asset:write'])]
    private ?string $owner = null;

    #[ORM\Column]
    #[Groups(['Asset:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255)]
    #[Groups(['Asset:read'])]
    private ?string $createdBy = null;

    #[ORM\OneToMany(mappedBy: 'asset', targetEntity: AssetAudit::class)]
    #[Groups(['Asset:read'])]
    private Collection $assetAudits;

    public function __construct()
    {
        $this->assetAudits = new ArrayCollection();
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

    public function getOwner(): ?string
    {
        return $this->owner;
    }

    public function setOwner(?string $owner): self
    {
        $this->owner = $owner;

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
}
