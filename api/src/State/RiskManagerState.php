<?php

namespace App\State;

use App\ApiResource\RiskManager as RiskManagerApi;
use App\Entity\RiskManager as RiskManagerEntity;

use App\Service\RiskManagerService;

use App\State\RogerStateFacade;

final class RiskManagerState extends RogerState
{

    public function __construct(
        RogerStateFacade $facade,
        RiskManagerService $service,
    ) {
        parent::__construct($facade, $service);
    }

    public function newApi(): RiskManagerApi
    {
        return new RiskManagerApi();
    }

    public function fromApiToEntity($api, $entity): RiskManagerEntity
    {
        if ($entity->getIdentifier() === null)
            $entity->setIdentifier($api->__get('identifier'));

        $entity = $api->fromApiToEntity($entity);
        
        return $entity;
    }

    public function fromEntityToApi($entity, $api): RiskManagerApi
    {
        $api->identifier = $entity->getIdentifier();
        $api->values = $entity->getValues();
        $api->valuesAggregator = $entity->getValuesAggregator();
        $api->triggers = $entity->getTriggers();

        return $api;
    }

}
