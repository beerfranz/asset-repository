<?php

namespace App\MessageHandler;

use App\Message\RogerAsyncMessage;

use App\Message\TaskMessage;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Psr\Log\LoggerInterface;

class RogerRouter
{

  public function __construct(
    private LoggerInterface $logger,
    private MessageBusInterface $eventBus,
  )
  {

  }

  // #[AsMessageHandler]
  public function handler(RogerAsyncMessage $message)
  {
    $event = $message->getEvent();
    $context = $message->getContext();

    if (isset($context['class']) && $context['class'] === '\App\Entity\Task') {

      $event = new TaskMessage($event, $context);
      $this->eventBus->dispatch(
          (new Envelope($event))
              ->with(new DispatchAfterCurrentBusStamp())
      );
    }

  
  }

}