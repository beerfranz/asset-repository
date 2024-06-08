<?php

namespace App\Audits\State;

use App\Audits\ApiResource\Audit as AuditApi;
use App\Audits\Entity\Audit as AuditEntity;
use App\Audits\Service\AuditService;

use Beerfranz\RogerBundle\State\RogerState;
use Beerfranz\RogerBundle\State\RogerStateFacade;

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

    protected function getCollection($context)
    {
        if (isset($context['uri_variables']) && count($context['uri_variables']) > 0) {
            foreach($context['uri_variables'] as $key => $value) {
                $context['filters'][$key] = $value;
            }
        }

        if (!isset($context['filters']['order']))
            $context['filters']['order'] = [ 'id' => 'desc' ];

        return $this->service->getCollection($context);
    }
}
