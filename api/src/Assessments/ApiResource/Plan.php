<?php

namespace App\Assessments\ApiResource;

use App\Assessments\State\PlanState;

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


#[ApiResource(
	description: 'Assessment plans',
	processor: PlanState::class,
	provider: PlanState::class,
	normalizationContext: ['groups' => ['AssessmentPlan:read']],
	denormalizationContext: ['groups' => ['AssessmentPlan:write']],
	routePrefix: '/assessments',
)]
#[GetCollection(
	security: "is_granted('ASSET_READ')",
	normalizationContext: ['groups' => ['AssessmentPlans:read']],
)]
#[Get(
	security: "is_granted('ASSET_READ')",
)]
#[Post(security: "is_granted('ASSET_WRITE')")]
#[Put(security: "is_granted('ASSET_WRITE')")]
#[Delete(security: "is_granted('ASSET_WRITE')")]
class Plan extends RogerApiResource
{
	#[Groups(['AssessmentPlans:read', 'AssessmentPlan:read', 'AssessmentPlan:write'])]
	#[ApiProperty(identifier: true)]
	public $identifier;

	#[Groups(['AssessmentPlans:read', 'AssessmentPlan:read', 'AssessmentPlan:write'])]
	public $title;

	#[Groups(['AssessmentPlans:read', 'AssessmentPlan:read', 'AssessmentPlan:write'])]
	public $asset;

	#[Groups(['AssessmentPlans:read', 'AssessmentPlan:read', 'AssessmentPlan:write'])]
	public array $tasks = [];

	#[Groups(['AssessmentPlans:read', 'AssessmentPlan:read'])]
	public int $taskCount = 0;

}
