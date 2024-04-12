<?php

namespace App\State;

use App\ApiResource\RiskManager as RiskManagerApi;
use App\Entity\RiskManager as RiskManagerEntity;

use App\Service\RiskManagerService;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

use Psr\Log\LoggerInterface;

final class RiskManagerState extends RogerState
{
    protected $riskManagerRepo;

    public function __construct(
        RequestStack $request,
        LoggerInterface $logger,
        Security $security,
        RiskManagerService $service,
    ) {
        parent::__construct($request, $logger, $security, $service);
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
