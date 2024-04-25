<?php

namespace App\Service;

use App\Entity\Indicator;
use App\Entity\IndicatorValue;
use App\Entity\RogerEntityInterface;
use App\Message\IndicatorValueMessage;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;

use Psr\Log\LoggerInterface;

class IndicatorValueService extends RogerService
{
  protected $indicatorRepo;

  public function __construct(
    EntityManagerInterface $entityManager,
    LoggerInterface $logger,
    protected MessageBusInterface $bus,
  ) {
    parent::__construct($entityManager, $logger, IndicatorValue::class);

    $this->indicatorRepo = $entityManager->getRepository(Indicator::class);
  }

  public function newEntity(): IndicatorValue
  {
    $entity = new IndicatorValue();

    return $entity;
  }

  public function findOneByIdentifiers(array $identifiers): null|IndicatorValue
  {
    $indicator = $this->findOneIndicatorByIdentifier($identifiers['indicatorIdentifier']);
    
    if ($indicator === null)
      throw new NotFoundHttpException('Not found indicator with identifier ' . $identifiers['indicatorIdentifier']);

    return $this->repo->findOneByIndicatorAndIdentifier($indicator, $identifiers['identifier']);
  }

  public function findOneIndicatorByIdentifier($identifier): ?Indicator
  {
    return $this->indicatorRepo->findOneByIdentifier($identifier);
  }

  public function sendMessage(IndicatorValue $indicatorValue): void
  {

    $context = [
      'indicatorValue' => [
        'identifier' => $indicatorValue->getIdentifier(),
      ]
    ];

    $indicator = $indicatorValue->getIndicator();

    if ($indicator !== null) {
      $context['indicatorValue']['indicator'] = [
        'identifier' => $indicator->getIdentifier(),
      ];

      $taskTemplateIdentifier = $indicator->getTaskTemplateIdentifier();

      if ($taskTemplateIdentifier !== null) {
        $context['indicatorValue']['indicator']['taskTemplate'] = [
          'identifier' => $taskTemplateIdentifier,
        ];
      }
    }

    $this->bus->dispatch(new IndicatorValueMessage('update_indicator_value', $context));
  }

  public function persistEntity(RogerEntityInterface $entity)
  {
    parent::persistEntity($entity);

    $this->sendMessage($entity);
  }
}
