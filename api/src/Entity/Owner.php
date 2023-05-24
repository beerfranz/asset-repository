<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;

use App\Repository\OwnerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[UniqueEntity(fields: ['name'], message: 'There is already an asset type with this name')]
#[ApiResource(
    normalizationContext: ['groups' => ['AssetOwner:read']],
    denormalizationContext: ['groups' => ['AssetOwner:write']],
    security: "is_granted('ASSET_READ')",
)]
#[GetCollection]
#[Post(security: "is_granted('ASSET_WRITE')")]
#[Get]
#[Put(security: "is_granted('ASSET_WRITE')")]
#[Patch(security: "is_granted('ASSET_WRITE')")]
#[Delete(security: "is_granted('ASSET_WRITE')")]
#[ORM\Entity(repositoryClass: OwnerRepository::class)]
#[ORM\UniqueConstraint(columns:["name"])]
class Owner
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[ApiProperty(identifier: false)]
    #[Groups(['AssetOwner:read', 'Asset:read', 'AssetDefinition:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[ApiProperty(identifier: true)]
    #[Groups(['AssetOwner:read', 'AssetOwner:write', 'Asset:read', 'Asset:write', 'AssetDefinition:read'])]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Asset::class)]
    #[Groups(['AssetOwner:read', 'AssetOwner:write'])]
    private Collection $assets;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: AssetDefinition::class)]
    private Collection $assetDefinitions;

    public function __construct()
    {
        $this->assets = new ArrayCollection();
        $this->assetDefinitions = new ArrayCollection();
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
            $asset->setOwner($this);
        }

        return $this;
    }

    public function removeAsset(Asset $asset): self
    {
        if ($this->assets->removeElement($asset)) {
            // set the owning side to null (unless already changed)
            if ($asset->getOwner() === $this) {
                $asset->setOwner(null);
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
            $assetDefinition->setOwner($this);
        }

        return $this;
    }

    public function removeAssetDefinition(AssetDefinition $assetDefinition): self
    {
        if ($this->assetDefinitions->removeElement($assetDefinition)) {
            // set the owning side to null (unless already changed)
            if ($assetDefinition->getOwner() === $this) {
                $assetDefinition->setOwner(null);
            }
        }

        return $this;
    }
}
