<?php

namespace App\MessageHandler;

use App\Message\TaskNotification;
use App\Message\IndicatorValueMessage;
use App\Service\TaskService;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

class TaskNotificationHandler
{

  public function __construct(
    private TaskService $service,
  )
  {

  }

  #[AsMessageHandler]
  public function newIndicatorValue(IndicatorValueMessage $message)
  {
    $event = $message->getEvent();

    if ($event === 'create_indicator_value') {
      $taskTemplate = $this->service->findOneTaskTemplateByIdentifier('TPL-test-data-with-workflow');

      $this->service->generateTaskFromTaskTemplate($taskTemplate, 'indicator');
    }
  }

}