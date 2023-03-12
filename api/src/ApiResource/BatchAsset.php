<?php

namespace App\ApiResource;

use App\State\BatchAssetProcessor;
use App\Entity\Asset;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\Post;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ApiResource(
    description: 'Batch asset',
    processor: BatchAssetProcessor::class,
    denormalizationContext: ['groups' => ['asset:write']],
)]
#[Post(security: "is_granted('ASSET_WRITE')")]
class BatchAsset
{
    #[Groups(['asset:write'])]
    public $assets;
}
