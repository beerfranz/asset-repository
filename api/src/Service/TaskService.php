<?php

namespace App\Service;

use App\Entity\Frequency;
use App\Entity\Task;
use App\Entity\TaskTemplate;

use App\Service\FrequencyService;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Scheduler\Trigger\CronExpressionTrigger;

use Psr\Log\LoggerInterface;

class TaskService extends RogerService
{
  protected $frequencyService;
  protected $taskRepo;
  protected $taskTemplateRepo;
  protected $userTemplateService;

  public function __construct(
    EntityManagerInterface $entityManager,
    LoggerInterface $logger,
    FrequencyService $frequencyService,
    UserTemplate $userTemplateService,
  ) {
    parent::__construct($entityManager, $logger, Task::class);

    $this->frequencyService = $frequencyService;
    $this->userTemplateService = $userTemplateService;

    $this->taskRepo = $entityManager->getRepository(Task::class);
    $this->taskTemplateRepo = $entityManager->getRepository(TaskTemplate::class);
  }

  public function newEntity(): Task
  {
    $task = new Task();

    return $task;
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

  public function possibleNextStatus(Task $task): null|array
  {
    $taskTemplate = $task->getTaskTemplate();

    if ($taskTemplate === null) {
      return null;
    }

    $taskTemplateWorkflow = $taskTemplate->getTaskWorkflow();

    if ($taskTemplateWorkflow === null) {
      return null;
    }

    $statusWorkflow = $taskTemplateWorkflow['statuses'][$task->getStatus()];

    if ($statusWorkflow === null) {
      return [];
    }

    $result = [];

    foreach ($statusWorkflow['nextStatuses'] as $nextStatus) {
      
      $constraintsAreValid = true;

      foreach ($taskTemplateWorkflow['statuses'][$nextStatus]['constraints'] as $constraint) {
        $check = $this->userTemplateService->test($constraint, [ 'owner' => $task->getOwner() ]);

        if (!$check->getBoolResult())
          $constraintsAreValid = false;
      }

      if ($constraintsAreValid === true)
        $result[] = $nextStatus;
    }

    return $result;
  }

  public function askNewStatus(Task $task, string $desiredStatus)
  {
    $taskTemplate = $task->getTaskTemplate();

    if ($taskTemplate === null) {
      return $this->updateStatus($task, $desiredStatus);
    }

    $taskTemplateWorkflow = $taskTemplate->getTaskWorkflow();

    if ($taskTemplateWorkflow === null) {
      return $this->updateStatus($task, $desiredStatus);
    }

    $statusWorkflow = $taskTemplateWorkflow['statuses'][$task->getStatus()];

    if ($statusWorkflow === null) {
      return $this->updateStatus($task, $desiredStatus);
    }

    // asked status not allowed in the workflow
    if (! in_array($desiredStatus, $statusWorkflow['nextStatuses']))
      return false;

    $desiredStatusWorkflow = $taskTemplateWorkflow[$desiredStatus];

    // check asked status constrains
    foreach ($desiredStatusWorkflow['constraints'] as $constraint) {
      $check = $this->userTemplateService->test($constraint, [ 'owner' => $task->getOwner() ]);

      if (!$check->getBoolResult())
        return false;
    }

    return $this->updateStatus($task, $desiredStatus);
  }

  protected function updateStatus(Task $task, string $status, bool $isDone = false) {
    $task->setStatus($desiredStatus);
    $task->setIsDone($isDone);

    $this->entityManager->persist($task);
    $this->entityManager->flush();
    $this->entityManager->clear();
    return $task;
  }
}