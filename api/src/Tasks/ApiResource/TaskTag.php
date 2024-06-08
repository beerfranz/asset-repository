<?php

namespace App\Tasks\ApiResource;

use App\Tasks\State\TaskTagState;

use Beerfranz\RogerBundle\ApiResource\RogerApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;

use Symfony\Component\Serializer\Annotation\Groups;

use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ApiResource(
    description: 'Task tags',
    processor: TaskTagState::class,
    provider: TaskTagState::class,
    normalizationContext: ['groups' => ['TaskTag:read']],
    denormalizationContext: ['groups' => ['TaskTag:write']],
)]
#[GetCollection(
    security: "is_granted('ASSET_READ')",
    normalizationContext: ['groups' => ['TaskTags:read']],
)]
#[Get(
    security: "is_granted('ASSET_READ')",
)]
#[Post(security: "is_granted('ASSET_WRITE')")]
#[Put(security: "is_granted('ASSET_WRITE')")]
#[Patch(
    security: "is_granted('ASSET_WRITE')",
)]
#[Delete(security: "is_granted('ASSET_WRITE')")]
class TaskTag extends RogerApiResource
{
    #[Groups(['TaskTags:read', 'TaskTag:read'])]
    #[ApiProperty(identifier: true)]
    public $id;

    #[Groups(['TaskTags:read', 'TaskTag:read', 'TaskTag:write'])]
    public $name;

    #[Groups(['TaskTags:read', 'TaskTag:read', 'TaskTag:write'])]
    public $value;

    #[Groups(['TaskTags:read', 'TaskTag:read', 'TaskTag:write'])]
    public $color;

}
