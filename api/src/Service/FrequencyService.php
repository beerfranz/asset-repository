<?php

namespace App\Service;

use App\Entity\Frequency;
use App\Entity\Indicator;

use Doctrine\ORM\EntityManagerInterface;

use Psr\Log\LoggerInterface;

class FrequencyService
{
  protected $logger;
  protected $entityManager;
  protected $indicatorRepo;

  public function __construct(
    EntityManagerInterface $entityManager,
    LoggerInterface $logger,
  ) {
    $this->entityManager = $entityManager;
    $this->logger = $logger;

    $this->indicatorRepo = $entityManager->getRepository(Indicator::class);
  }

  public function test()
  {
    foreach ($this->indicatorRepo->getFrequencyToUpdate() as $indicator) {
      $cronExpression = new CronExpressionTrigger($indicator['crontab']);

      $nextRun = $cronExpression->getNextRunDate(new \DateTimeImmutable);

      
    }
  }

}