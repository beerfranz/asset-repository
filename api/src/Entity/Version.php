<?php

namespace App\Entity;

use App\Repository\VersionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: VersionRepository::class)]
class Version
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['Version:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['Version:read', 'Version:name', 'Asset:read'])]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'version', targetEntity: Asset::class)]
    #[Groups(['Version:read'])]
    private Collection $assets;

    #[ORM\ManyToOne(inversedBy: 'versions')]
    #[Groups(['Version:read'])]
    private ?AssetDefinition $assetDefinition = null;

    public function __construct()
    {
        $this->assets = new ArrayCollection();
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
            $asset->setVersion($this);
        }

        return $this;
    }

    public function removeAsset(Asset $asset): self
    {
        if ($this->assets->removeElement($asset)) {
            // set the owning side to null (unless already changed)
            if ($asset->getVersion() === $this) {
                $asset->setVersion(null);
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
}
