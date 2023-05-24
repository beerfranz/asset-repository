<?php

namespace App\ApiResource;

use App\State\BatchAssetProcessor;
use App\Entity\Asset;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ApiResource(
    description: 'Batch asset v2',
    processor: BatchAssetProcessor::class,
    denormalizationContext: ['groups' => ['asset:write']],
)]
#[Post(security: "is_granted('ASSET_WRITE')")]
#[Put(security: "is_granted('ASSET_WRITE')")]
class BatchAssetv2
{

    #[Groups(['asset:write'])]
    private $asset;

    public function __construct()
    {
        $this->asset = new ArrayCollection();
    }

    /**
     * @return Collection<int, Asset>
     */
    public function getAssets(): Collection
    {
        return $this->asset;
    }

    public function addAsset(Asset $asset): self
    {
        if(!$this->asset->contains($asset)) {
            $this->asset->add($asset);
            // $asset->setObj() // Useless ^^
        }

        return $this;
    }

    public function removeAsset(Asset $asset): self
    {
        $this->asset->remove($asset);

        return $this;
    }
}
