<?php

namespace App\State;

use App\ApiResource\TaskWorkflow as TaskWorkflowApi;
use App\Entity\TaskWorkflow as TaskWorkflowEntity;
use App\Entity\TaskWorkflowStatus;

use App\Service\TaskWorkflowService;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

use Psr\Log\LoggerInterface;

final class TaskWorkflowState extends RogerState
{

    public function __construct(
        RequestStack $request,
        LoggerInterface $logger,
        Security $security,
        TaskWorkflowService $service,
    ) {
        parent::__construct($request, $logger, $security, $service);
    }

    public function newApi(): TaskWorkflowApi
    {
        return new TaskWorkflowApi();
    }

    public function fromApiToEntity($api, $entity): TaskWorkflowEntity
    {
        if ($entity->getIdentifier() === null)
            $entity->setIdentifier($api->__get('identifier'));

        $workflow = [];

        $statusesWorkflow = [];
        foreach($api->__get('statuses') as $status => $attributes) {
            $statusesWorkflow[$status] = new TaskWorkflowStatus(array_merge($attributes, [ 'status' => $status ]));
        }

        $workflow['statuses'] = $statusesWorkflow;

        $entity->setWorkflow($workflow);
        
        return $entity;
    }

    public function fromEntityToApi($entity, $api): TaskWorkflowApi
    {
        $this->simpleFromEntityToApi($entity, $api);

        $workflow = $entity->getWorkflow();

        $api->statuses = $workflow['statuses'];

        return $api;
    }
}