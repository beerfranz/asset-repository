<?php

namespace App\ApiResource;

use App\State\AssetProcessor;
use App\Entity\AssetAudit;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints;

use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ApiResource(
    normalizationContext: ['groups' => ['Asset:read']],
    denormalizationContext: ['grAssetoups' => ['Asset:write']],
    security: "is_granted('ASSET_READ')"
)]
#[GetCollection]
#[Post(security: "is_granted('ASSET_WRITE')")]
#[Get]
#[Put(security: "is_granted('ASSET_WRITE')")]
#[Patch(security: "is_granted('ASSET_WRITE')")]
#[Delete(security: "is_granted('ASSET_WRITE')")]
#[UniqueEntity(fields: ['identifier'], message: 'There is already an asset this identifier')]
class Assetv2
{
    #[Groups(['Asset:read'])]
    public ?int $id = null;

    #[Groups(['Asset:read', 'Asset:write'])]
    public ?string $identifier = null;

    #[Groups(['Asset:read', 'Asset:write'])]
    public ?string $type = null;

    #[Groups(['Asset:read', 'Asset:write'])]
    public array $attributes = [];

    #[Groups(['Asset:read'])]
    public ?\DateTimeImmutable $createdAt = null;

    #[Groups(['Asset:read'])]
    public ?string $createdBy = null;

    #[Groups(['Asset:read'])]
    private Collection $assetAudits;

    #[Groups(['Asset:read', 'Asset:write'])]
    public ?string $owner = null;

    public ?string $source = null;

    public function __construct()
    {
        $this->assetAudits = new ArrayCollection();
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
