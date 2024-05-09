<?php

namespace App\State;

use App\ApiResource\Audit as AuditApi;
use App\Entity\Audit as AuditEntity;
use App\Service\AuditService;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class AuditState extends RogerState
{

    public function __construct(
        RogerStateFacade $facade,
        AuditService $service,
    ) {
        parent::__construct($facade, $service);
    }

    public function newApi(): AuditApi
    {
        return new AuditApi();
    }

    public function fromApiToEntity($api, $entity): AuditEntity
    {
        $entity = $api->fromApiToEntity($entity);        

        return $entity;
    }

    public function fromEntityToApi($entity, $api): AuditApi
    {
        $this->simpleFromEntityToApi($entity, $api);

        return $api;
    }
}
