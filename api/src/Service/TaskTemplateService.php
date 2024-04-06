<?php

namespace App\Service;

use App\Entity\Task;
use App\Entity\TaskTemplate;
use App\Entity\TaskWorkflow;
use App\Entity\TaskWorkflowStatus;

use Doctrine\ORM\EntityManagerInterface;

use Psr\Log\LoggerInterface;

class TaskTemplateService extends RogerService
{

  public function __construct(
    EntityManagerInterface $entityManager,
    LoggerInterface $logger,
  ) {
    parent::__construct($entityManager, $logger, TaskTemplate::class);
  }

  public function newEntity(): TaskTemplate
  {
    $taskTemplate = new TaskTemplate();

    return $taskTemplate;
  }

}
