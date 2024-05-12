<?php

namespace App\ApiResource;

use App\State\SettingState;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;

use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    description: 'Settings',
    processor: SettingState::class,
    provider: SettingState::class,
    normalizationContext: ['groups' => ['Setting:read']],
    denormalizationContext: ['groups' => ['Setting:write']],
)]
#[GetCollection(
    security: "is_granted('ASSET_READ')",
    normalizationContext: ['groups' => ['Settings:read']],
)]
#[Get(
    security: "is_granted('ASSET_READ')",
)]
class Setting extends RogerApiResource
{
    #[Groups(['Settings:read', 'Setting:read'])]
    #[ApiProperty(identifier: true)]
    public $identifier;

    #[Groups(['Settings:read', 'Setting:read'])]
    public $value;

}
