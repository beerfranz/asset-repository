<?php

namespace App\Service;

use App\Entity\Indicator;
use App\Entity\IndicatorValue;
use App\Entity\TaskTemplate;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Psr\Log\LoggerInterface;

class IndicatorService extends RogerService
{

  protected $indicatorValueRepo;

  public function __construct(
    EntityManagerInterface $entityManager,
    LoggerInterface $logger,
  ) {
    parent::__construct($entityManager, $logger, Indicator::class);
    $this->indicatorValueRepo = $entityManager->getRepository(IndicatorValue::class);
    $this->taskTemplateRepo = $entityManager->getRepository(TaskTemplate::class);
  }

  public function newEntity(): Indicator
  {
    $entity = new Indicator();

    return $entity;
  }

  public function findIndicatorSample(Indicator $indicator)
  {
    return $this->indicatorValueRepo->findIndicatorSample($indicator);
  }

  public function findOneTaskTemplateByIdentifier(string $taskTemplateIdentifier)
  {
    return $this->findOneByIdentifierInRepo($taskTemplateIdentifier, $this->taskTemplateRepo);
  }

}
