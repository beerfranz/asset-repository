<?php

namespace App\Entity;

use App\Repository\EnvironmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity(fields: ['identifier'], message: 'There is already an environment with this identifier')]
#[ORM\Entity(repositoryClass: EnvironmentRepository::class)]
#[ORM\UniqueConstraint(columns:["identifier"])]
class Environment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['Environment:read', 'Asset:read', 'Instance:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['Environment:read', 'Environment:write', 'Asset:read', 'Instance:read', 'Asset:environment'])]
    private ?string $identifier = null;

    #[ORM\Column(length: 255)]
    #[Groups(['Environment:read', 'Environment:write', 'Asset:read', 'Instance:read'])]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'environment', targetEntity: Asset::class)]
    #[Groups(['Environment:read', 'Environment:write'])]
    private Collection $assets;

    public function __construct()
    {
        $this->assets = new ArrayCollection();
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
            $asset->setEnvironment($this);
        }

        return $this;
    }

    public function removeAsset(Asset $asset): self
    {
        if ($this->assets->removeElement($asset)) {
            // set the owning side to null (unless already changed)
            if ($asset->getEnvironment() === $this) {
                $asset->setEnvironment(null);
            }
        }

        return $this;
    }
}
