<?php

namespace App\Service;

use App\Entity\Task;
use App\Entity\TaskTemplate;
use App\Entity\TaskWorkflow;
use App\Entity\TaskType;

use App\Service\TaskService;

use Doctrine\ORM\EntityManagerInterface;

use Psr\Log\LoggerInterface;

class TaskTemplateService extends RogerService
{

  protected $taskTypeRepo;

  public function __construct(
    EntityManagerInterface $entityManager,
    LoggerInterface $logger,
    protected TaskService $taskService,
  ) {
    parent::__construct($entityManager, $logger, TaskTemplate::class);

    $this->taskTypeRepo = $entityManager->getRepository(TaskType::class);
  }

  public function newEntity(): TaskTemplate
  {
    $taskTemplate = new TaskTemplate();

    return $taskTemplate;
  }

  public function findOneTaskTypeByIdentifier($identifier)
  {
    return $this->taskTypeRepo->findOneByIdentifier($identifier);
  }

  public function generateTaskFromTaskTemplate(TaskTemplate $taskTemplate, $identifier): Task
  {
    return $this->taskService->generateTaskFromTaskTemplate($taskTemplate, $identifier);
  }

}
