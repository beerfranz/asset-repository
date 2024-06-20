<?php

namespace App\Tasks\ApiResource;

use App\Tasks\State\TaskAuthorizationState;

use Beerfranz\RogerBundle\ApiResource\RogerApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;

use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    description: 'Task authorizations',
    provider: TaskAuthorizationState::class,
)]
#[GetCollection(
    security: "is_granted('ROLE_ADMIN')",
)]
class TaskAuthorization extends RogerApiResource
{
    public string $relation;
}
