<?php

namespace App\Tasks\State;

use App\Tasks\ApiResource\TaskType as TaskTypeApi;
use App\Tasks\Entity\TaskType as TaskTypeEntity;

use App\Tasks\Service\TaskTypeService;

use Beerfranz\RogerBundle\State\RogerState;
use Beerfranz\RogerBundle\State\RogerStateFacade;

final class TaskTypeState extends RogerState
{

	public function __construct(
		RogerStateFacade $facade,
		TaskTypeService $service,
	) {
		parent::__construct($facade, $service);
	}

	public function newApi(): TaskTypeApi
	{
		return new TaskTypeApi();
	}

	public function fromApiToEntity($api, $entity): TaskTypeEntity
	{
		if ($entity->getIdentifier() === null)
			$entity->setIdentifier($api->__get('identifier'));

		$entity->setTaskWorkflow($this->service->findOneTaskWorkflowByIdentifier($api->__get('workflowIdentifier')));
		
		return $entity;
	}

	public function fromEntityToApi($entity, $api): TaskTypeApi
	{
		$api->identifier = $entity->getIdentifier();

		$taskWorkflow = $entity->getTaskWorkflow();
		if ($taskWorkflow !== null) {
			$api->workflowIdentifier = $taskWorkflow->getIdentifier();
		}

		return $api;
	}
}
