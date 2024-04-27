<?php

namespace App\MessageHandler;

use App\Message\TaskMessage;
use App\Message\RogerAsyncMessage;
use App\Service\IndicatorService;
use App\Service\IndicatorValueService;
use App\MessageHandler\RogerHandlerTrait;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Psr\Log\LoggerInterface;


class IndicatorHandler
{

  use RogerHandlerTrait;

  public function __construct(
    private IndicatorService $service,
    private IndicatorValueService $valueService,
    private LoggerInterface $logger,
  )
  {

  }

  #[AsMessageHandler]
  public function taskHandler(RogerAsyncMessage $message)
  {
    $event = $message->getEvent();
    $context = $message->getContext();

    if ($this->isAboutEntityNames($context, [ 'Task' ])) {


      if ($this->isTaskDone($context)) {

        if (
          isset($context['entity']['attributes']['indicator']['identifier']) &&
          isset($context['entity']['attributes']['indicatorValue']['identifier']) &&
          isset($context['entity']['attributes']['indicatorValue']['value'])
        ) {

          $indicatorIdentifier = $context['entity']['attributes']['indicator']['identifier'];
          $indicatorValueIdentifier = $context['entity']['attributes']['indicatorValue']['identifier'];

          $indicatorValue = $this->valueService->findOneByIdentifiers([ 'indicatorIdentifier' => $indicatorIdentifier, 'identifier' => $indicatorValueIdentifier ]);
          $indicatorValue->setValue($context['entity']['attributes']['indicatorValue']['value']);
          $indicatorValue->setIsValidated(true);
          $this->valueService->persistEntity($indicatorValue);
        }
      }
      
    }
  
  }

  protected function isTaskStatusChanged(array $context) {
    if (isset($context['diffs']['status']))
      return true;

    return false;
  }

  protected function isTaskDone(array $context) {
    if (isset($context['entity']['isDone']) && $context['entity']['isDone'] == 1)
      return true;

    return false;
  }

}