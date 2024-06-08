<?php

namespace App\Assessments\ApiResource;

use App\Assessments\State\TemplateState;

use Beerfranz\RogerBundle\ApiResource\RogerApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Serializer\Filter\GroupFilter;

use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
	processor: TemplateState::class,
	provider: TemplateState::class,
    normalizationContext: ['groups' => ['AssessmentTemplate:read']],
    denormalizationContext: ['groups' => ['AssessmentTemplate:write']],
    security: "is_granted('ASSET_READ')",
    routePrefix: '/assessments',
)]
#[GetCollection]
#[Post(security: "is_granted('ASSET_WRITE')")]
#[Get]
#[Put(security: "is_granted('ASSET_WRITE')")]
#[Patch(security: "is_granted('ASSET_WRITE')")]
#[Delete(security: "is_granted('ASSET_WRITE')")]
#[Post(
	name: 'generate_assessment_plan_from_template',
	uriTemplate: '/templates/{identifier}/generate-plan',
	uriVariables: [ 'identifier' ] ,
	security: "is_granted('ASSET_WRITE')",
	normalizationContext: [ 'groups' => [ 'AssessmentTemplateGenerate' ]],
	denormalizationContext: [ 'groups' => [ 'AssessmentTemplateGenerate' ]],
	input: GeneratePlan::class,
)]

class Template extends RogerApiResource
{
	#[Groups(['AssessmentTemplates:read', 'AssessmentTemplate:read', 'AssessmentTemplate:write'])]
	#[ApiProperty(identifier: true)]
	public ?string $identifier = null;

	#[Groups(['AssessmentTemplates:read', 'AssessmentTemplate:read', 'AssessmentTemplate:write'])]
	public ?string $title = null;

	#[Groups(['AssessmentTemplates:read', 'AssessmentTemplate:read', 'AssessmentTemplate:write'])]
	public ?array $taskTemplates = [];

	#[Groups(['AssessmentTemplates:read', 'AssessmentTemplate:read'])]
	public int $taskTemplatesCount = 0;

	#[Groups(['AssessmentTemplates:read', 'AssessmentTemplate:read', 'AssessmentTemplate:write'])]
	public ?array $assets = [];

	#[Groups(['AssessmentTemplates:read', 'AssessmentTemplate:read'])]
	public int $assetsCount = 0;

	#[Groups(['AssessmentTemplates:read', 'AssessmentTemplate:read'])]
	public ?array $rules = null;

}