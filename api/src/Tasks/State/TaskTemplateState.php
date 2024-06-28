<?php

namespace App\Tasks\State;

use App\Tasks\ApiResource\TaskTemplate as TaskTemplateApi;
use App\Tasks\ApiResource\TaskTemplateGenerateDto;
use App\Tasks\Entity\TaskTemplate as TaskTemplateEntity;
use App\Tasks\Service\TaskTemplateService;
use App\Service\FrequencyService;
use App\Tasks\Service\TaskWorkflowService;

use App\Tasks\ApiResource\TaskType;

use ApiPlatform\Metadata\Operation;

use Beerfranz\RogerBundle\State\RogerState;
use Beerfranz\RogerBundle\State\RogerStateFacade;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class TaskTemplateState extends RogerState
{
	protected $frequencyService;

	public function __construct(
		RogerStateFacade $facade,
		TaskTemplateService $service,
		FrequencyService $frequencyService,
		protected TaskWorkflowService $taskWorkflowService,
	) {
		parent::__construct($facade, $service);
		$this->frequencyService = $frequencyService;
	}

	public function newApi(): TaskTemplateApi
	{
		return new TaskTemplateApi();
	}

	public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
	{
		$operationInputs = $operation->getInput();
		if (isset($operationInputs['name']) && $operationInputs['name'] === 'TaskTemplateGenerateDto')
			return new TaskTemplateGenerateDto();
		
		return $this->stateProvide($operation, $uriVariables, $context);
	}

	/**
	 * @param $data
	 * @return T2
	 */
	public function process(mixed $api, Operation $operation, array $uriVariables = [], array $context = [])
	{
		if ($api instanceof TaskTemplateGenerateDto)
		{
			$taskIdentifier = $uriVariables['taskIdentifier'];
			$taskTemplateIdentifier = $uriVariables['identifier'];

			$taskTemplate = $this->getEntityByIdentifier($taskTemplateIdentifier);

			return $this->service->generateTaskFromTaskTemplate($taskTemplate, $taskIdentifier);
		}
		
		return $this->stateProcess($api, $operation, $uriVariables, $context);
	}

	public function fromApiToEntity($api, $entity): TaskTemplateEntity
	{
		if ($entity->getIdentifier() === null)
			$entity->setIdentifier($api->__get('identifier'));

		$entity->hydrator([
			'title' => $api->title,
			'description' => $api->description,
			'frequency' => $api->frequency,
		]);

		if ($entity->getFrequency() !== [])
			$entity = $this->frequencyService->calculateNextIteration($entity);

		$worflow_identifier = $api->__get('workflow_identifier');
		if ($worflow_identifier !== null) {
			$entity->setTaskWorkflow($this->taskWorkflowService->findOneByIdentifier($worflow_identifier));
		}

		if ($api->__get('parent') !== null)
			$entity->setParent($this->service->findOneByIdentifier($api->__get('parent')->__get('identifier')));
		if ($api->__get('parentIdentifier') !== null)
			$entity->setParent($this->service->findOneByIdentifier($api->__get('parentIdentifier')));

		if ($api->__get('type') !== null)
			$entity->setTaskType($this->service->findOneTaskTypeByIdentifier($api->__get('type')->__get('identifier')));
		if ($api->__get('typeIdentifier') !== null)
			$entity->setTaskType($this->service->findOneTaskTypeByIdentifier($api->__get('typeIdentifier')));

		foreach ($api->tags as $name => $opts) {
			$entity->addTag($this->service->getTag($name, $opts));
		}
		
		return $entity;
	}

	public function fromEntityToApi($entity, $api): TaskTemplateApi
	{
		$api->hydrator([
			'identifier' => $entity->getIdentifier(),
			'title' => $entity->getTitle(),
			'description' => $entity->getDescription(),
			'frequency' => $entity->getFrequency(),
		]);

		try {
			$api->__set('workflow', $entity->getTaskType()->getTaskWorkflow()->getWorkflow());
		} catch(\Error $e) {}

		if (null !== $entity->getParent()) {
			$api->parentIdentifier = $entity->getParent()->getIdentifier();
			$api->parent = new TaskTemplateApi([ 'identifier' => $entity->getParent()->getIdentifier()]);
		}

		if (null !== $entity->getTaskType()) {
			$api->typeIdentifier = $entity->getTaskType()->getIdentifier();
			$api->type = new TaskType(['identifier' => $entity->getTaskType()->getIdentifier()]);
		}

		$tags = [];
		try {
			foreach ($entity->getTags() as $tag) {
				$tags[$tag->getName()] = [ 'value' => $tag->getValue(), 'color' => $tag->getColor() ];
			}
		} catch(\Error $e) {}

		$api->__set('tags', $tags);

		return $api;
	}
}
