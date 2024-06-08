<?php

namespace App\Setting\State;

use App\Setting\ApiResource\Setting as SettingApi;
use App\Setting\Entity\Setting as SettingEntity;
use App\Setting\Service\SettingService;

use Beerfranz\RogerBundle\State\RogerState;
use Beerfranz\RogerBundle\State\RogerStateFacade;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class SettingState extends RogerState
{

	public function __construct(
		RogerStateFacade $facade,
		SettingService $service,
	) {
		parent::__construct($facade, $service);
	}

	public function newApi(): SettingApi
	{
		return new SettingApi();
	}

	public function fromApiToEntity($api, $entity): SettingEntity
	{
		$entity = $api->fromApiToEntity($entity);        

		return $entity;
	}

	public function fromEntityToApi($entity, $api): SettingApi
	{
		$this->simpleFromEntityToApi($entity, $api);

		return $api;
	}

}
