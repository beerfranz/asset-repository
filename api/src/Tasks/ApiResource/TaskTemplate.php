<?php

namespace App\Tasks\ApiResource;

use Beerfranz\RogerBundle\Filter\AutocompleteFilter;

use App\Tasks\State\TaskTemplateState;
use App\Tasks\ApiResource\TaskTemplateGenerateDto;

use App\Tasks\Entity\TaskTag;
use App\Tasks\ApiResource\TaskType;

use Beerfranz\RogerBundle\ApiResource\RogerApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Serializer\Filter\GroupFilter;

use Symfony\Component\Serializer\Annotation\Groups;

use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ApiFilter(GroupFilter::class, arguments: ['parameterName' => 'groups', 'overrideDefaultGroups' => true, 'whitelist' => ['TaskType:identifier']])]
#[ApiFilter(AutocompleteFilter::class, properties: [ 'taskType.identifier'])]
#[ApiResource(
	description: 'Task template',
	processor: TaskTemplateState::class,
	provider: TaskTemplateState::class,
	normalizationContext: ['groups' => ['TaskTemplate:read']],
	denormalizationContext: ['groups' => ['TaskTemplate:write']],
)]
#[GetCollection(
	security: "is_granted('TASK_READ')",
	normalizationContext: ['groups' => ['TaskTemplates:read']],
)]
#[Get(
	security: "is_granted('TASK_READ')",
)]
#[Post(security: "is_granted('TASK_WRITE')")]
#[Put(security: "is_granted('TASK_WRITE')")]
#[Delete(security: "is_granted('TASK_WRITE')")]
#[Put(
	name: 'generate_tasks_from_template',
	uriTemplate: '/task_templates/{identifier}/generate/{taskIdentifier}',
	uriVariables: [ 'identifier', 'taskIdentifier' ] ,
	security: "is_granted('TASK_WRITE')",
	normalizationContext: [ 'groups' => [ 'TaskTemplateGenerate' ]],
	denormalizationContext: [ 'groups' => [ 'TaskTemplateGenerate' ]],
	input: TaskTemplateGenerateDto::class,
	// output: AssetDefinitionBatchDto::class,
)]
class TaskTemplate extends RogerApiResource
{
	#[Groups(['TaskTemplates:read', 'TaskTemplate:read', 'TaskTemplate:write'])]
	#[ApiProperty(identifier: true)]
	public $identifier;

	#[Groups(['TaskTemplates:read', 'TaskTemplate:read', 'TaskTemplate:write'])]
	public $title;

	#[Groups(['TaskTemplates:read', 'TaskTemplate:read', 'TaskTemplate:write'])]
	public $description;

	#[Groups(['TaskTemplates:read', 'TaskTemplate:read', 'TaskTemplate:write'])]
	#[ApiProperty(
		openapiContext: [ "type" => "object" ]
	)]
	public ?array $frequency = [];

	#[Groups(['TaskTemplate:read'])]
	#[ApiProperty(
		openapiContext: [ "type" => "object" ]
	)]
	public ?array $workflow = [];

	#[Groups(['TaskTemplates:read', 'TaskTemplate:read', 'TaskTemplate:write'])]
	public $workflow_identifier;

	public function setTaskWorkflow($taskWorkflow) {
		if (isset($taskWorkflow['identifier']))
			$this->workflow_identifier = $taskWorkflow['identifier'];

		if (isset($taskWorkflow['workflow']))
			$this->workflow = $taskWorkflow['workflow'];
		return $this;
	}
	
	#[Groups(['TaskTemplates:read', 'TaskTemplate:read', 'TaskTemplate:write'])]
	public TaskTemplate $parent;

	#[Groups(['TaskTemplates:read', 'TaskTemplate:read', 'TaskTemplate:write'])]
	public TaskType $type;

	#[Groups(['TaskTemplates:read', 'TaskTemplate:read', 'TaskTemplate:write'])]
	#[ApiProperty(
		openapiContext: [ "type" => "object" ]
	)]
	public ?array $tags = [];

}
