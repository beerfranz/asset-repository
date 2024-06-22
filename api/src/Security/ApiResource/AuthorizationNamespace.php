<?php

namespace App\Security\ApiResource;

use App\Security\State\AuthorizationNamespaceState;

use Beerfranz\RogerBundle\ApiResource\RogerApiResource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;

use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    description: 'Task authorizations',
    provider: AuthorizationNamespaceState::class,
    routePrefix: '/admin',
)]
#[GetCollection(
    security: "is_granted('ROLE_ADMIN')",
)]
class AuthorizationNamespace extends RogerApiResource
{
    #[ApiProperty(identifier: true)]
    public string $namespace;

    public string $link;
}
