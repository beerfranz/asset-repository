<?php

namespace App\ApiResource;

use App\State\BatchInstanceProcessor;

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
    description: 'Batch instance',
    processor: BatchInstanceProcessor::class,
    denormalizationContext: ['groups' => ['Instance:write']],
)]
#[Post(security: "is_granted('ASSET_WRITE')")]
#[Put(security: "is_granted('ASSET_WRITE')")]
class BatchInstance
{
    #[Groups(['Instance:write'])]
    public $instances;

    #[Groups(['Instance:write'])]
    public $source = null;
}
