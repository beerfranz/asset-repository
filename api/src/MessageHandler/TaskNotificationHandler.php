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
    $context = $message->getContext();

    if ($event === 'update_indicator_value' && isset($context['indicatorValue']['indicator']['taskTemplate']['identifier'])) {
      
      $messageIndicatorValue = $context['indicatorValue'];
      $messageIndicator = $messageIndicatorValue['indicator'];
      $messageTaskTemplate = $messageIndicator['taskTemplate'];

      $taskTemplate = $this->service->findOneTaskTemplateByIdentifier($messageTaskTemplate['identifier']);

      $properties = [];
      $properties['attributes'] = [
        'indicator' => [
          'identifier' => $messageIndicator['identifier'],
        ],
        'indicatorValue' => [
          'identifier' => $messageIndicatorValue['identifier'],
        ],
        'relatedTo' => [
          'indicatorValue' => '/indicators/' . $messageIndicator['identifier'] . '/values/' . $messageIndicatorValue['identifier'],
        ]
      ];

      $task = $this->service->generateTaskFromTaskTemplate(
        $taskTemplate,
        $messageIndicator['identifier'] . '_' . $messageIndicatorValue['identifier'],
        null,
        $properties,
      );
    }
  }

}