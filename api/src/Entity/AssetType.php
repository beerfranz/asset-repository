<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use App\Repository\AssetTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

# $this->addSql('INSERT INTO asset_type (name) VALUES ("container"), ("helm"), ("pod"), ("cloud provider"), ("virtual machine")');

#[ORM\Entity(repositoryClass: AssetTypeRepository::class)]
#[ORM\UniqueConstraint(columns:["name"])]
#[UniqueEntity(fields: ['name'], message: 'There is already an asset type with this name')]
#[ApiResource(
    normalizationContext: ['groups' => ['AssetType:read']],
    denormalizationContext: ['groups' => ['AssetType:write']],
    security: "is_granted('ASSET_READ')",
)]
#[GetCollection]
#[Post(security: "is_granted('ASSET_WRITE')")]
#[Get]
#[Put(security: "is_granted('ASSET_WRITE')")]
#[Patch(security: "is_granted('ASSET_WRITE')")]
#[Delete(security: "is_granted('ASSET_WRITE')")]
class AssetType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[ApiProperty(identifier: false)]
    #[Groups(['AssetType:read', 'AssetType:write'])]
    private int $id;

    #[ORM\Column(length: 255)]
    #[ApiProperty(identifier: true)]
    #[Groups(['AssetType:read', 'AssetType:write'])]
    private string $name;

    #[ORM\OneToMany(mappedBy: 'type', targetEntity: Asset::class)]
    #[Groups(['AssetType:read'])]
    private Collection $assets;

    public function __construct()
    {
        $this->assets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
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
            $asset->setType($this);
        }

        return $this;
    }

    public function removeAsset(Asset $asset): self
    {
        if ($this->assets->removeElement($asset)) {
            // set the owning side to null (unless already changed)
            if ($asset->getType() === $this) {
                $asset->setType(null);
            }
        }

        return $this;
    }
}
