<?php

namespace App\Service;

use App\Entity\Frequency;
use App\Entity\Indicator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Scheduler\Trigger\CronExpressionTrigger;

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

  public function setNextIteration()
  {
    foreach ($this->indicatorRepo->getFrequencyToUpdate() as $indicator) {
      $frequency = $indicator->getFrequency();
      $cronExpression = CronExpressionTrigger::fromSpec($frequency['crontab']);

      $nextRun = $cronExpression->getNextRunDate(new \DateTimeImmutable);

      $frequency['nextIterationDate'] = $nextRun;
      
      $indicator->setFrequency($frequency);
      
      $this->entityManager->persist($indicator);
      $this->entityManager->flush();
      $this->entityManager->clear();
    }
  }

}