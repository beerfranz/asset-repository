<?php

namespace App\Service;

use App\Entity\Frequency;
use App\Entity\Task;
use App\Entity\TaskTemplate;

use App\Service\FrequencyService;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Scheduler\Trigger\CronExpressionTrigger;

use Psr\Log\LoggerInterface;

class TaskService
{
  protected $logger;
  protected $entityManager;
  protected $frequencyService;
  protected $taskRepo;
  protected $taskTemplateRepo;

  public function __construct(
    EntityManagerInterface $entityManager,
    LoggerInterface $logger,
    FrequencyService $frequencyService,
  ) {
    $this->entityManager = $entityManager;
    $this->logger = $logger;
    $this->frequencyService = $frequencyService;

    $this->taskRepo = $entityManager->getRepository(Task::class);
    $this->taskTemplateRepo = $entityManager->getRepository(TaskTemplate::class);
  }

  public function generateTasksFromTaskTemplate() {
    $taskTemplateIds = $this->taskTemplateRepo->getTaskToGenerate();

    foreach ($taskTemplateIds as $taskTemplateId) {
      $taskTemplate = $this->taskTemplateRepo->find($taskTemplateId);

      $datetime = new \DateTimeImmutable();

      $taskIdentifier = $taskTemplate->getIdentifier() . '_' . $datetime->format('Ymd');

      $task = $this->taskRepo->findOneByIdentifier($taskIdentifier);

      if ($task === null) {
        $task = new Task();
        $task->setIdentifier($taskIdentifier);
        $task->setTaskTemplate($taskTemplate);
        $task->setCreatedAt($datetime);
      }

      $task->setTitle($taskTemplate->getTitle());
      $task->setDescription($taskTemplate->getDescription());
      
      $this->entityManager->persist($task);

      $this->frequencyService->calculateNextIteration($taskTemplate);
      $this->entityManager->persist($task);
      $this->entityManager->flush();
      $this->entityManager->clear();
    }
  }
}