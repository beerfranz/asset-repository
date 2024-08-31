<?php

namespace App\Assessments\ApiResource;

use App\Assessments\State\AssessmentsAuthorizationState;

use Beerfranz\RogerBundle\ApiResource\RogerApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;

use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    description: 'Assessments authorizations',
    provider: AssessmentsAuthorizationState::class,
    routePrefix: '/assessments',
)]
#[GetCollection(
    security: "is_granted('ROLE_ADMIN')",
)]
class Authorization extends RogerApiResource
{
    public string $relation;

    public bool $hasContextSupport = false;

    public bool $hasAttributeSupport = false;
}
