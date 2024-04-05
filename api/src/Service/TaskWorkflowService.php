<?php

namespace App\Service;

use App\Entity\Task;
use App\Entity\TaskTemplate;
use App\Entity\TaskWorkflow;
use App\Entity\TaskWorkflowStatus;

use Doctrine\ORM\EntityManagerInterface;

use Psr\Log\LoggerInterface;

class TaskWorkflowService extends RogerService
{

  public function __construct(
    EntityManagerInterface $entityManager,
    LoggerInterface $logger,
  ) {
    parent::__construct($entityManager, $logger, TaskWorkflow::class);
  }

  public function newEntity(): TaskWorkflow
  {
    $taskWorkflow = new TaskWorkflow();

    return $taskWorkflow;
  }

}