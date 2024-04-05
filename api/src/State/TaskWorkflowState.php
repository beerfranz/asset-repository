<?php

namespace App\State;

use App\ApiResource\TaskWorkflow as TaskWorkflowApi;
use App\Entity\TaskWorkflow as TaskWorkflowEntity;
use App\Entity\TaskWorkflowStatus;
use App\Service\TaskWorkflowService;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\State\ProviderInterface;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RequestStack;

use Psr\Log\LoggerInterface;

final class TaskWorkflowState extends CommonState implements ProcessorInterface, ProviderInterface
{
    protected $taskWorkflowRepo;
    protected $service;

    public function __construct(
        EntityManagerInterface $entityManager,
        RequestStack $request,
        LoggerInterface $logger,
        Security $security,
        TaskWorkflowService $service,
    ) {
        parent::__construct($entityManager, $request, $logger, $security);
        $this->taskWorkflowRepo = $entityManager->getRepository(TaskWorkflowEntity::class);
        $this->service = $service;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface)
        {
            $taskWorkflowEntities = $this->getCollection($this->taskWorkflowRepo, $context);

            $output = [];
            foreach($taskWorkflowEntities as $taskWorkflowEntity) {
                $taskWorkflowApi = new TaskWorkflowApi();
                $output[] = $taskWorkflowApi->fromEntityToApi($taskWorkflowEntity);
            }

            return $output;
        }

        $taskWorkflowEntity = $this->taskWorkflowRepo->findOneByIdentifier($uriVariables['identifier']);
        $taskWorkflowApi = new TaskWorkflowApi();

        if ($taskWorkflowEntity === null)
            return $taskWorkflowApi;

        $taskWorkflowApi->fromEntityToApi($taskWorkflowEntity);

        return $taskWorkflowApi->fromEntityToApi($taskWorkflowEntity);
    }
    
    /**
     * @param $data
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $user = $this->security->getUser();

        if ($operation instanceof Delete) {
            $taskWorkflow = $this->getEntity($uriVariables['identifier']);
            $this->deleteEntity($taskWorkflow);
        } else {
            if (isset($uriVariables['identifier']))
                $data->identifier = $uriVariables['identifier'];

            $taskWorkflow = $this->processOneTaskWorkflow($data);

            $data->id = $taskWorkflow->getId();
        }
    }

    protected function getEntity($identifier)
    {
        return $this->taskWorkflowRepo->findOneByIdentifier($identifier);
    }

    protected function processOneTaskWorkflow($data): TaskWorkflowEntity
    {
        $entity = null;

        $identifier = $data->__get('identifier');

        if ($identifier !== null) {
            $entity = $this->getEntity($identifier);
        }
        
        if ($entity === null)
        {
            $entity = new TaskWorkflowEntity();
        }

        $this->fromApiToEntity($data, $entity);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        $this->entityManager->clear();

        return $entity;
    }

    protected function fromApiToEntity($api, TaskWorkflowEntity $entity): TaskWorkflowEntity
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
}
