<?php

namespace App\ApiResource;

use App\State\TaskTypeState;

use Beerfranz\RogerBundle\ApiResource\RogerApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;

use Symfony\Component\Serializer\Annotation\Groups;

use Symfony\Component\Serializer\Annotation\Context;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ApiResource(
    description: 'Task type',
    processor: TaskTypeState::class,
    provider: TaskTypeState::class,
    normalizationContext: ['groups' => ['TaskType:read']],
    denormalizationContext: ['groups' => ['TaskType:write']],
)]
#[GetCollection(
    security: "is_granted('ASSET_READ')",
    normalizationContext: ['groups' => ['TaskTypes:read']],
)]
#[Get(
    security: "is_granted('ASSET_READ')",
)]
#[Post(security: "is_granted('ASSET_WRITE')")]
#[Put(security: "is_granted('ASSET_WRITE')")]
#[Delete(security: "is_granted('ASSET_WRITE')")]
class TaskType extends RogerApiResource
{
    #[Groups(['TaskTypes:read', 'TaskType:read', 'TaskType:write'])]
    #[ApiProperty(identifier: true)]
    public $identifier;

    #[Groups(['TaskTypes:read', 'TaskType:read', 'TaskType:write'])]
    public $workflowIdentifier;

}
