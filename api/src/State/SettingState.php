<?php

namespace App\State;

use App\ApiResource\Setting as SettingApi;
use App\Entity\Setting as SettingEntity;
use App\Service\SettingService;

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
