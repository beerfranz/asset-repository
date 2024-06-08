<?php

namespace App\Assessments\State;

use App\Assessments\ApiResource\Plan as PlanApi;
use App\Assessments\Entity\AssessmentPlan as PlanEntity;

use App\Assessments\Service\PlanService;

use Beerfranz\RogerBundle\State\RogerState;
use Beerfranz\RogerBundle\State\RogerStateFacade;

final class PlanState extends RogerState
{

	public function __construct(
		RogerStateFacade $facade,
		PlanService $service,
	) {
		parent::__construct($facade, $service);
	}

	public function newApi(): PlanApi
	{
		return new PlanApi();
	}

	public function fromApiToEntity($api, $entity): PlanEntity
	{
		// if ($entity->getIdentifier() === null)
			$entity->setIdentifier($api->__get('identifier'));

		$entity->setTitle($api->title);

		if ($api->asset !== null)
			$entity->setAsset($this->service->findOneAssetByIdentifier($api->asset));

		foreach ($api->tasks as $taskIdentifier) {
			$entity->addTask($this->service->findOneTaskByIdentifier($taskIdentifier));
		}
		
		return $entity;
	}

	public function fromEntityToApi($entity, $api): PlanApi
	{
		$api->identifier = $entity->getIdentifier();
		$api->title = $entity->getTitle();
		if ($entity->getAsset() !== null)
			$api->asset = $entity->getAsset()->getIdentifier();

		$api->taskCount = count($entity->getTasks());

		foreach ($entity->getTasks() as $task) {
			$api->tasks[] = $task->getIdentifier();
		}


		return $api;
	}

}
