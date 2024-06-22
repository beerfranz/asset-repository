<?php

namespace App\Security\ApiResource;

use App\Security\State\AuthorizationRoleState;

use Beerfranz\RogerBundle\ApiResource\RogerApiResource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;

use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    description: 'Roles authorizations',
    provider: AuthorizationRoleState::class,
    routePrefix: '/admin',
)]
#[GetCollection(
    security: "is_granted('ROLE_ADMIN')",
)]
class AuthorizationRole extends RogerApiResource
{
    #[ApiProperty(identifier: true)]
    public string $identifier;

    public string $label;

    public string $description;
}
