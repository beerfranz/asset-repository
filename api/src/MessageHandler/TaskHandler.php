<?php

namespace App\MessageHandler;

use App\Message\TaskNotification;
use App\Message\IndicatorValueMessage;
use App\Message\RogerAsyncMessage;
use App\MessageHandler\RogerHandlerAbstract;
use App\Service\TaskService;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Psr\Log\LoggerInterface;


class TaskHandler extends RogerHandlerAbstract
{

  public function __construct(
    protected TaskService $service,
    protected LoggerInterface $logger,
  )
  {

  }

  #[AsMessageHandler]
  public function indicatorValueMessage(IndicatorValueMessage $message)
  {
    $this->handlerName = __METHOD__;
    $this->messageClass = $message::class;

    $this->logReceiveMessage();

    $event = $message->getEvent();
    $context = $message->getContext();

    if ($this->isAboutEntityNames($context, [ 'IndicatorValue' ]) && isset($context['entity']['indicator']['taskTemplateIdentifier'])) {
      $this->logProcessingMessage();
      $messageIndicatorValue = $context['entity'];
      $messageIndicator = $messageIndicatorValue['indicator'];
      $messageTaskTemplateIdentifier = $messageIndicator['taskTemplateIdentifier'];

      $taskTemplate = $this->service->findOneTaskTemplateByIdentifier($messageTaskTemplateIdentifier);

      $properties = [];
      $properties['attributes'] = [
        'indicator' => [
          'identifier' => $messageIndicator['identifier'],
        ],
        'indicatorValue' => [
          'identifier' => $messageIndicatorValue['identifier'],
        ],
        'relatedTo' => [
          'indicatorValue' => [
            'value' => '/indicators/' . $messageIndicator['identifier'] . '/values/' . $messageIndicatorValue['identifier'],
            'kind' => 'link',
            'title' => 'Indicator Value',
          ]
        ]
      ];

      try {
        $task = $this->service->generateTaskFromTaskTemplate(
          $taskTemplate,
          $messageIndicator['identifier'] . '_' . $messageIndicatorValue['identifier'],
          null,
          $properties,
        );
      } catch (\Exception $e) {
        $this->logger->error('Failed to generate task for indicator value ' . $messageIndicator['identifier'] . '_' . $messageIndicatorValue['identifier']);
        throw $e;
      }
    } else {
      $this->logIgnoringMessage('not about entity IndicatorValue');
    }
  }

}