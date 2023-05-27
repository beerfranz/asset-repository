<?php

namespace App\ApiResource;

use App\State\BatchAssetProcessor;

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
    description: 'Batch asset',
    processor: BatchAssetProcessor::class,
    denormalizationContext: ['groups' => ['Asset:write']],
)]
#[Post(security: "is_granted('ASSET_WRITE')")]
#[Put(security: "is_granted('ASSET_WRITE')")]
class BatchAsset
{
    #[Groups(['Asset:write'])]
    public $assets;

    #[Groups(['Asset:write'])]
    public $source = null;

    #[Groups(['Asset:write'])]
    public $owner = null;
}