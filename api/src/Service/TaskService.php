<?php

namespace App\Service;

use App\Entity\Frequency;
use App\Entity\Task;
use App\Entity\TaskType;
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

  protected function getTaskWorkflow(Task $task)
  {
    $taskType = $task->getTaskType();

    if ($taskType === null) {
      return null;
    }

    $taskTypeWorkflow = $taskType->getTaskWorkflow();

    return $taskTypeWorkflow;
  }

  public function possibleNextStatus(Task $task): null|array
  {
    $taskTypeWorkflow = $this->getTaskWorkflow($task);

    if ($taskTypeWorkflow === null) {
      return null;
    }

    $statusWorkflow = $taskTypeWorkflow->getWorkflow();

    if ($statusWorkflow === null) {
      return [];
    }

    $result = [];

    foreach ($statusWorkflow['statuses'][$task->getStatus()]->getNextStatuses() as $nextStatus) {
      
      $constraintsAreValid = true;

      foreach ($statusWorkflow['statuses'][$nextStatus]->getConstraints() as $constraint) {
        $check = $this->userTemplateService->test($constraint, [ 'owner' => $task->getOwner() ]);

        if (!$check->getBoolResult())
          $constraintsAreValid = false;
      }

      if ($constraintsAreValid === true)
        $result[] = $nextStatus;
    }

    return $result;
  }

  public function setTaskTemplate(Task $task, ?TaskTemplate $taskTemplate)
  {
    if ($taskTemplate !== null)
      $task->setTaskTemplate($taskTemplate);

    return $task;
  }

  public function getDefaultStatus(TaskTemplate $taskTemplate): ?string
  {
    $taskTemplateWorkflow = $taskTemplate->getTaskWorkflow();

    if ($taskTemplateWorkflow === null)
      return null;

    $taskWorkflow = $taskTemplateWorkflow->getWorkflow();

    foreach($taskWorkflow['statuses'] as $status => $taskWorkflowStatus) {
      if ($taskWorkflowStatus->getIsDefault() === true)
        return $status;
    }

    return null;
  }

  public function askNewStatus(Task $task, string $desiredStatus)
  {
    $taskTemplateWorkflow = $taskTemplate->getTaskWorkflow($task);

    if ($taskTemplateWorkflow === null) {
      return $this->updateStatus($task, $desiredStatus);
    }

    $statusWorkflow = $taskTemplateWorkflow['statuses'][$task->getStatus()];

    if ($statusWorkflow === null) {
      return $this->updateStatus($task, $desiredStatus);
    }

    // asked status not allowed in the workflow
    if (! in_array($desiredStatus, $statusWorkflow->getNextStatuses()))
      return false;

    $desiredStatusWorkflow = $taskTemplateWorkflow['statuses'][$desiredStatus];

    // check asked status constrains
    foreach ($desiredStatusWorkflow->getConstraints() as $constraint) {
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

  public function findOneTaskTemplateByIdentifier($identifier): TaskTemplate|null
  {
    return $this->taskTemplateRepo->findOneByIdentifier($identifier);
  }
}