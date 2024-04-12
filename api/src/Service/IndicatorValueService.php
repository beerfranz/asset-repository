<?php

namespace App\Service;

use App\Entity\Indicator;
use App\Entity\IndicatorValue;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Psr\Log\LoggerInterface;

class IndicatorValueService extends RogerService
{

  public function __construct(
    EntityManagerInterface $entityManager,
    LoggerInterface $logger,
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
}
