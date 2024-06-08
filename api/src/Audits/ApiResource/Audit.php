<?php

namespace App\Audits\ApiResource;

use App\Audits\State\AuditState;
use App\Audits\Entity\Audit as AuditEntity;

use Beerfranz\RogerBundle\ApiResource\RogerApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;

use Symfony\Component\Serializer\Annotation\Groups;

use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ApiResource(
    description: 'Audit',
    processor: AuditState::class,
    provider: AuditState::class,
    normalizationContext: ['groups' => ['Audit:read']],
    denormalizationContext: ['groups' => ['Audit:write']],
)]
#[GetCollection(
    security: "is_granted('ASSET_READ')",
    normalizationContext: ['groups' => ['Audits:read']],
)]
#[Get(
    security: "is_granted('ASSET_READ')",
)]

#[GetCollection(
    uriTemplate: '/audits/subject-kinds/{subjectKind}/subjects/{subject}',
    uriVariables: [ 'subjectKind', 'subject' ],
)]
#[GetCollection(
    uriTemplate: '/audits/subject-kinds/{subjectKind}',
    uriVariables: [ 'subjectKind' ],
)]
#[GetCollection(
    uriTemplate: '/audits/subjects/{subject}',
    uriVariables: [ 'subject' ],
)]
class Audit extends RogerApiResource
{
    #[Groups(['Audits:read', 'Audit:read'])]
    #[ApiProperty(identifier: true)]
    public $id;

    #[Groups(['Audits:read', 'Audit:read'])]
    public $subjectKind;

    #[Groups(['Audits:read', 'Audit:read'])]
    public $subject;

    #[Groups(['Audits:read', 'Audit:read'])]
    public $actor;

    #[Groups(['Audits:read', 'Audit:read'])]
    public $datetime;

    #[Groups(['Audits:read', 'Audit:read'])]
    public $action;

    #[Groups(['Audit:read'])]
    public $data;

}
