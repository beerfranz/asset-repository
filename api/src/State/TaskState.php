<?php

namespace App\State;

use App\ApiResource\Task as TaskApi;
use App\Entity\Task as TaskEntity;
use App\Service\TaskService;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RequestStack;

use Psr\Log\LoggerInterface;

final class TaskState extends RogerState
{

    public function __construct(
        RequestStack $request,
        LoggerInterface $logger,
        Security $security,
        TaskService $service,
    ) {
        parent::__construct($request, $logger, $security, $service);
    }

    public function newApi(): TaskApi
    {
        return new TaskApi();
    }

    public function fromApiToEntity($api, $entity): TaskEntity
    {
        if ($entity->getIdentifier() === null) {
            $entity->setIdentifier($api->__get('identifier'));
            $entity->setCreatedAt(new \DateTimeImmutable());
        }

        $entity->setTitle($api->__get('title'));
        $entity->setDescription($api->__get('description'));
        $entity->setAssignedTo($api->__get('assignedTo'));
        $entity->setOwner($api->__get('owner'));

        $taskTemplate = $this->service->findOneTaskTemplateByIdentifier($api->__get('taskTemplateIdentifier'));
        $this->service->setTaskTemplate($entity, $taskTemplate);

        $status = $api->__get('status');
        if ($status !== null && $this->service->askNewStatus($entity, $status))
            $entity->setStatus($api->__get('status'));
        
        if ($entity->getStatus() === null && $entity->getTaskTemplate() !== null) {
            $workflowDefaultStatus = $this->service->getDefaultStatus($entity->getTaskTemplate());
            if ($workflowDefaultStatus !== null)
                $entity->setStatus($this->service->getDefaultStatus($entity->getTaskTemplate()));
        }

        return $entity;
    }

    public function fromEntityToApi($entity, $api): TaskApi
    {
        $this->simpleFromEntityToApi($entity, $api);

        $api->__set('allowedNextStatuses', $this->service->possibleNextStatus($entity));

        return $api;
    }
}
