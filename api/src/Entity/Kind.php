<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Serializer\Filter\GroupFilter;

use App\Filter\AutocompleteFilter;
use App\Repository\KindRepository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: KindRepository::class)]
#[ORM\UniqueConstraint(columns:["identifier"])]
#[ApiResource(
    normalizationContext: ['groups' => ['Kind:read']],
    denormalizationContext: ['groups' => ['Kind:write']],
    security: "is_granted('ASSET_READ')"
)]
#[ApiFilter(OrderFilter::class, properties: ['id', 'identifier'])]
#[ApiFilter(AutocompleteFilter::class, properties: [ 'identifier' ])]
#[ApiFilter(GroupFilter::class, arguments: ['parameterName' => 'groups', 'overrideDefaultGroups' => true, 'whitelist' => ['Kind:identifier']])]
#[UniqueEntity(fields: ['identifier'], message: 'There is already a kind with this identifier')]
class Kind
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['Kind:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['Kind:read', 'Kind:write', 'Asset:read', 'Kind:identifier'])]
    private ?string $identifier = null;

    #[ORM\OneToMany(mappedBy: 'kind', targetEntity: Asset::class)]
    #[Groups(['Kind:read'])]
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
            $asset->setKind($this);
        }

        return $this;
    }

    public function removeAsset(Asset $asset): self
    {
        if ($this->assets->removeElement($asset)) {
            // set the owning side to null (unless already changed)
            if ($asset->getKind() === $this) {
                $asset->setKind(null);
            }
        }

        return $this;
    }
}
