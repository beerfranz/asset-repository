<?php

namespace App\Tasks\State;

use App\Tasks\ApiResource\TaskTag as TaskTagApi;
use App\Tasks\Entity\TaskTag as TaskTagEntity;

use App\Tasks\Service\TaskTagService;

use Beerfranz\RogerBundle\State\RogerState;
use Beerfranz\RogerBundle\State\RogerStateFacade;

final class TaskTagState extends RogerState
{

	public function __construct(
		RogerStateFacade $facade,
		TaskTagService $service,
	) {
		parent::__construct($facade, $service);
	}

	public function newApi(): TaskTagApi
	{
		return new TaskTagApi();
	}

	public function fromApiToEntity($api, $entity): TaskTagEntity
	{
		return $entity;
	}

	public function fromEntityToApi($entity, $api): TaskTagApi
	{
		$this->simpleFromEntityToApi($entity, $api);

		return $api;
	}
}
