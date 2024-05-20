<?php

namespace App\Indicators\MessageHandler;

use App\Message\TaskMessage;
use Beerfranz\RogerBundle\Message\RogerAsyncMessage;
use App\Message\IndicatorValueMessage;
use App\Indicators\Service\IndicatorService;
use App\Indicators\Service\IndicatorValueService;
use Beerfranz\RogerBundle\MessageHandler\RogerHandlerAbstract;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Psr\Log\LoggerInterface;


class IndicatorHandler extends RogerHandlerAbstract
{

  public function __construct(
    protected IndicatorService $service,
    protected IndicatorValueService $valueService,
    protected LoggerInterface $logger,
  )
  {

  }

  #[AsMessageHandler]
  public function taskMessage(TaskMessage $message)
  {
    $this->handlerName = __METHOD__;
    $this->messageClass = $message::class;

    $event = $message->getEvent();
    $context = $message->getContext();

    $this->logReceiveMessage();

    if ($this->isAboutEntityNames($context, [ 'Task' ])) {

      if ($this->isTaskDone($context)) {

        if (
          isset($context['entity']['attributes']['indicator']['identifier']) &&
          isset($context['entity']['attributes']['indicatorValue']['identifier']) &&
          isset($context['entity']['attributes']['indicatorValue']['value'])
        ) {

          $this->logProcessingMessage();

          $indicatorIdentifier = $context['entity']['attributes']['indicator']['identifier'];
          $indicatorValueIdentifier = $context['entity']['attributes']['indicatorValue']['identifier'];

          $indicatorValue = $this->valueService->findOneByIdentifiers([ 'indicatorIdentifier' => $indicatorIdentifier, 'identifier' => $indicatorValueIdentifier ]);
          $indicatorValue->setValue($context['entity']['attributes']['indicatorValue']['value']);
          $indicatorValue->setIsValidated(true);
          $this->valueService->persistEntity($indicatorValue);
        } else
          $this->logIgnoringMessage('context not compete');
      } else
        $this->logIgnoringMessage('task not done');
    } else {
      $this->logIgnoringMessage('not about entity Task');
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