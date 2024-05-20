<?php

namespace App\Tasks\ApiResource;

use App\Tasks\State\TaskWorkflowState;

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
	description: 'Task workflow',
	processor: TaskWorkflowState::class,
	provider: TaskWorkflowState::class,
	normalizationContext: ['groups' => ['TaskWorkflow:read']],
	denormalizationContext: ['groups' => ['TaskWorkflow:write']],
)]
#[GetCollection(
	security: "is_granted('ASSET_READ')",
	normalizationContext: ['groups' => ['TaskWorkflows:read']],
)]
#[Get(
	security: "is_granted('ASSET_READ')",
)]
#[Post(security: "is_granted('ASSET_WRITE')")]
#[Put(security: "is_granted('ASSET_WRITE')")]
#[Delete(security: "is_granted('ASSET_WRITE')")]
class TaskWorkflow extends RogerApiResource
{
	#[Groups(['TaskWorkflows:read', 'TaskWorkflow:read', 'TaskWorkflow:write'])]
	#[ApiProperty(identifier: true)]
	public $identifier;

	#[Groups(['TaskWorkflows:read', 'TaskWorkflow:read', 'TaskWorkflow:write'])]
	#[ApiProperty(
		openapiContext: [ "type" => "object" ]
	)]
	public ?array $statuses = [];

}
