<?php

namespace App\State;

use App\ApiResource\TaskTemplate as TaskTemplateApi;
use App\ApiResource\TaskTemplateGenerateDto;
use App\Entity\TaskTemplate as TaskTemplateEntity;
use App\Service\TaskTemplateService;
use App\Service\FrequencyService;
use App\Service\TaskWorkflowService;

use ApiPlatform\Metadata\Operation;

use Beerfranz\RogerBundle\State\RogerState;
use Beerfranz\RogerBundle\State\RogerStateFacade;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class TaskTemplateState extends RogerState
{
    protected $frequencyService;

    public function __construct(
        RogerStateFacade $facade,
        TaskTemplateService $service,
        FrequencyService $frequencyService,
        protected TaskWorkflowService $taskWorkflowService,
    ) {
        parent::__construct($facade, $service);
        $this->frequencyService = $frequencyService;
    }

    public function newApi(): TaskTemplateApi
    {
        return new TaskTemplateApi();
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $operationInputs = $operation->getInput();
        if (isset($operationInputs['name']) && $operationInputs['name'] === 'TaskTemplateGenerateDto')
            return new TaskTemplateGenerateDto();
        
        return $this->stateProvide($operation, $uriVariables, $context);
    }

    /**
     * @param $data
     * @return T2
     */
    public function process(mixed $api, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if ($api instanceof TaskTemplateGenerateDto)
        {
            $taskIdentifier = $uriVariables['taskIdentifier'];
            $taskTemplateIdentifier = $uriVariables['identifier'];

            $taskTemplate = $this->getEntityByIdentifier($taskTemplateIdentifier);

            return $this->service->generateTaskFromTaskTemplate($taskTemplate, $taskIdentifier);
        }
        
        return $this->stateProcess($api, $operation, $uriVariables, $context);
    }

    public function fromApiToEntity($api, $entity): TaskTemplateEntity
    {
        if ($entity->getIdentifier() === null)
            $entity->setIdentifier($api->__get('identifier'));

        $entity = $api->fromApiToEntity($entity);

        if ($entity->getFrequency() !== [])
            $entity = $this->frequencyService->calculateNextIteration($entity);

        $worflow_identifier = $api->__get('workflow_identifier');
        if ($worflow_identifier !== null) {
            $entity->setTaskWorkflow($this->taskWorkflowService->findOneByIdentifier($worflow_identifier));
        }

        $entity->setParent($this->service->findOneByIdentifier($api->__get('parentIdentifier')));
        $entity->setTaskType($this->service->findOneTaskTypeByIdentifier($api->__get('typeIdentifier')));
        
        return $entity;
    }

    public function fromEntityToApi($entity, $api): TaskTemplateApi
    {
        $this->simpleFromEntityToApi($entity, $api);

        if (null !== $entity->getParent())
            $api->parentIdentifier = $entity->getParent()->getIdentifier();

        if (null !== $entity->getTaskType())
            $api->typeIdentifier = $entity->getTaskType()->getIdentifier();

        return $api;
    }
}
