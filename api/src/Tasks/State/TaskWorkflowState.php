<?php

namespace App\Tasks\State;

use App\Tasks\ApiResource\TaskWorkflow as TaskWorkflowApi;
use App\Tasks\Entity\TaskWorkflow as TaskWorkflowEntity;
use App\Tasks\Entity\TaskWorkflowStatus;

use App\Tasks\Service\TaskWorkflowService;

use Beerfranz\RogerBundle\State\RogerState;
use Beerfranz\RogerBundle\State\RogerStateFacade;

final class TaskWorkflowState extends RogerState
{

	public function __construct(
		RogerStateFacade $facade,
		TaskWorkflowService $service,
	) {
		parent::__construct($facade, $service);
	}

	public function newApi(): TaskWorkflowApi
	{
		return new TaskWorkflowApi();
	}

	public function fromApiToEntity($api, $entity): TaskWorkflowEntity
	{
		if ($entity->getIdentifier() === null)
			$entity->setIdentifier($api->__get('identifier'));

		$workflow = [];

		$statusesWorkflow = [];
		foreach($api->__get('statuses') as $status => $attributes) {
			$statusesWorkflow[$status] = new TaskWorkflowStatus(array_merge($attributes, [ 'status' => $status ]));
		}

		$workflow['statuses'] = $statusesWorkflow;

		$entity->setWorkflow($workflow);
		
		return $entity;
	}

	public function fromEntityToApi($entity, $api): TaskWorkflowApi
	{
		$this->simpleFromEntityToApi($entity, $api);

		$workflow = $entity->getWorkflow();

		$api->statuses = $workflow['statuses'];

		return $api;
	}
}
