<?php

namespace App\MessageHandler;

use App\Entity\Audit;
use Beerfranz\RogerBundle\Message\RogerAsyncMessage;

use App\Service\AuditService;
use Beerfranz\RogerBundle\MessageHandler\RogerHandlerAbstract;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Psr\Log\LoggerInterface;


class AuditHandler extends RogerHandlerAbstract
{

  public function __construct(
    protected AuditService $service,
    protected LoggerInterface $logger,
  )
  {

  }

  #[AsMessageHandler]
  public function rogerMessage(RogerAsyncMessage $message)
  {
    $this->handlerName = __METHOD__;
    $this->messageClass = $message::class;

    $event = $message->getEvent();
    $context = $message->getContext();

    $this->logReceiveMessage();

    // $this->logger->error('new audit message:' . print_r($context));

    $data = [];
    if ($context['action'] === 'create')
      $data = $context['entity'];

    if ($context['action'] === 'update')
      $data = $context['diffs'];

    if ($context['action'] === 'delete')
      $data = $context['entity'];

    if (!isset($context['actor']))
      $context['actor'] = 'unknown';

    if (isset($context['entity']['identifier']))
      $subject = $context['entity']['identifier'];
    else
      $subject = 'unknown';

    $audit = new Audit([
      'actor' => $context['actor'],
      'subject' => $subject,
      'subjectKind' => $context['className'],
      'action' => $context['action'],
      'datetime' => new \DateTimeImmutable($context['datetime']),
      'data' => $data,
    ]);

    $this->service->persistEntity($audit);
  
  }

}