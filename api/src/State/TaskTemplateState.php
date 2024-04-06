<?php

namespace App\State;

use App\ApiResource\TaskTemplate as TaskTemplateApi;
use App\Entity\TaskTemplate as TaskTemplateEntity;
use App\Service\TaskTemplateService;
use App\Service\FrequencyService;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RequestStack;

use Psr\Log\LoggerInterface;

final class TaskTemplateState extends RogerState
{
    protected $frequencyService;
    public function __construct(
        RequestStack $request,
        LoggerInterface $logger,
        Security $security,
        TaskTemplateService $service,
        FrequencyService $frequencyService,
    ) {
        parent::__construct($request, $logger, $security, $service);
        $this->frequencyService = $frequencyService;
    }

    public function newApi(): TaskTemplateApi
    {
        return new TaskTemplateApi();
    }

    public function fromApiToEntity($api, $entity): TaskTemplateEntity
    {
        if ($entity->getIdentifier() === null)
            $entity->setIdentifier($api->__get('identifier'));

        $entity = $api->fromApiToEntity($entity);

        if ($entity->getFrequency() !== [])
            $entity = $this->frequencyService->calculateNextIteration($entity);
        
        return $entity;
    }

    public function fromEntityToApi($entity, $api): TaskTemplateApi
    {
        $this->simpleFromEntityToApi($entity, $api);


        return $api;
    }
}
