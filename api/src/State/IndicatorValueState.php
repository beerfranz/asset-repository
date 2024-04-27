<?php

namespace App\State;

use App\ApiResource\IndicatorValue as IndicatorValueApi;
use App\Entity\Indicator;
use App\Entity\IndicatorValue as IndicatorValueEntity;
use App\Service\IndicatorValueService;
use App\Service\TriggerService;

use App\State\RogerStateFacade;

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
        $entity->setIsValidated($api->isValidated || false);

        $trigger = $this->triggerService->calculateTrigger($indicator->getTriggers(), $entity->getValue());
        $entity->setTrigger($trigger->toArray());

        return $entity;
    }

    public function fromEntityToApi($entity, $api): IndicatorValueApi
    {   
        return $api->fromEntityToApi($entity);
    }

}
