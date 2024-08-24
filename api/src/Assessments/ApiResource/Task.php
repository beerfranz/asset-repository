<?php

namespace App\Assessments\ApiResource;

use App\Tasks\ApiResource\Task as TaskBase;

use App\Assessments\State\TaskState;

use Beerfranz\RogerBundle\ApiResource\RogerApiResource;

use ApiPlatform\Metadata\ApiResource;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
	description: 'Assessment plans',
	processor: TaskState::class,
	provider: TaskState::class,
	normalizationContext: ['groups' => ['AssessmentPlan:read']],
	denormalizationContext: ['groups' => ['AssessmentPlan:write']],
	routePrefix: '/assessments',
)]
final class Task extends TaskBase
{
}
