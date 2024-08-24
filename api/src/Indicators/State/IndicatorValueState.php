<?php

namespace App\Indicators\State;

use App\Indicators\ApiResource\IndicatorValue as IndicatorValueApi;
use App\Indicators\Entity\Indicator;
use App\Indicators\Entity\IndicatorValue as IndicatorValueEntity;
use App\Indicators\Service\IndicatorValueService;
use App\Common\Service\TriggerService;

use Beerfranz\RogerBundle\State\RogerState;
use Beerfranz\RogerBundle\State\RogerStateFacade;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class IndicatorValueState extends RogerState
{

	public function __construct(
		RogerStateFacade $facade,
		IndicatorValueService $service,
		protected TriggerService $triggerService,
	) {
		parent::__construct($facade, $service);
	}

	public function newApi(): IndicatorValueApi
	{
		return new IndicatorValueApi();
	}

	public function fromApiToEntity($api, $entity): IndicatorValueEntity
	{
		$indicator = $this->service->findOneIndicatorByIdentifier($this->uriVariables['indicatorIdentifier']);

		if ($entity->getIdentifier() === null){
			$entity->setIdentifier($api->__get('identifier'));
			$entity->setIndicator($indicator);
			$entity->setDatetime(new \DateTimeImmutable());
			$entity->setIsValidated(false);
		}

		$entity->setValue($api->__get('value'));

		if ($api->__get('value') !== null && $api->__get('isValidated') === true)
			$this->service->validate($entity);

		if ($api->__get('isValidated') === false)   
			$entity->setIsValidated(false);

		$trigger = $this->triggerService->calculateTrigger($indicator->getTriggers(), $entity->getValue());
		$entity->setTrigger($trigger->toArray());

		return $entity;
	}

	public function fromEntityToApi($entity, $api): IndicatorValueApi
	{   
		return $api->fromEntityToApi($entity);
	}

}
