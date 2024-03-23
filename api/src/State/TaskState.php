<?php

namespace App\State;

use App\ApiResource\Task as TaskApi;
use App\Entity\Task;


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

final class TaskState extends CommonState implements ProcessorInterface, ProviderInterface
{
    protected $taskRepo;

    public function __construct(
        EntityManagerInterface $entityManager,
        RequestStack $request,
        LoggerInterface $logger,
        Security $security,
    ) {
        parent::__construct($entityManager, $request, $logger, $security);
        $this->taskRepo = $entityManager->getRepository(Task::class);
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface)
        {
            $taskEntities = $this->getCollection($this->taskRepo, $context);

            $output = [];
            foreach($taskEntities as $taskEntity) {
                $taskApi = new TaskApi();
                $output[] = $taskApi->populateFromTaskEntity($taskEntity);
            }

            return $output;
        }

        $taskEntity = $this->taskRepo->find($uriVariables['id']);
        $taskApi = new TaskApi();
        return $taskApi->populateFromTaskEntity($taskEntity);
    }
    
    /**
     * @param $data
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $user = $this->security->getUser();

        if ($operation instanceof Delete) {
            $this->delete($data);
        } else {
            if (isset($uriVariables['id']))
                $data->id = $uriVariables['id'];

            // if (isset($uriVariables['identifier']))
            //     $data->identifier = $uriVariables['identifier'];

            $task = $this->processOneTask((array) $data);

            $data->id = $task->getId();
        }
    }

    protected function processOneTask($data): Task
    {
        $task = null;

        if (isset($data['id'])) {
            $id = $data['id'];

            $task = $this->taskRepo->find($data['id']);
        }
        
        if ($task === null)
        {
            $task = new Task();
            $task->setCreatedAt(new \DateTimeImmutable());
        }

        if (isset($data['title']))
            $task->setTitle($data['title']);

        if (isset($data['description']))
            $task->setDescription($data['description']);

        $this->entityManager->persist($task);
        $this->entityManager->flush();
        $this->entityManager->clear();

        return $task;
    }

    protected function delete(Task $task) {
        $this->entityManager->remove($task);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }
}
