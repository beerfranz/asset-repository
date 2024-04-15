<?php

namespace App\State;

use App\ApiResource\TaskType as TaskTypeApi;
use App\Entity\TaskType as TaskTypeEntity;

use App\Service\TaskTypeService;

final class TaskTypeState extends RogerState
{

    public function __construct(
        RogerStateFacade $facade,
        TaskTypeService $service,
    ) {
        parent::__construct($facade, $service);
    }

    public function newApi(): TaskTypeApi
    {
        return new TaskTypeApi();
    }

    public function fromApiToEntity($api, $entity): TaskTypeEntity
    {
        if ($entity->getIdentifier() === null)
            $entity->setIdentifier($api->__get('identifier'));

        $entity->setTaskWorkflow($this->service->findOneTaskWorkflowByIdentifier($api->__get('workflowIdentifier')));
        
        return $entity;
    }

    public function fromEntityToApi($entity, $api): TaskTypeApi
    {
        $this->simpleFromEntityToApi($entity, $api);

        $taskWorkflow = $entity->getTaskWorkflow();
        if ($taskWorkflow !== null) {
            $api->workflowIdentifier = $taskWorkflow->getIdentifier();
        }

        return $api;
    }
}
