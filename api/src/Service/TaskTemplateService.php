<?php

namespace App\Service;

use App\Entity\Task;
use App\Entity\TaskTemplate;
use App\Entity\TaskWorkflow;
use App\Entity\TaskType;

use Doctrine\ORM\EntityManagerInterface;

use Psr\Log\LoggerInterface;

class TaskTemplateService extends RogerService
{

  protected $taskTypeRepo;

  public function __construct(
    EntityManagerInterface $entityManager,
    LoggerInterface $logger,
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

}
