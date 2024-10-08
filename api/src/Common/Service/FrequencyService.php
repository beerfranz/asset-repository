<?php

namespace App\Common\Service;

use App\Common\Entity\Frequency;
use App\Indicators\Entity\Indicator;
use App\Tasks\Entity\TaskTemplate;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Scheduler\Trigger\CronExpressionTrigger;

use Psr\Log\LoggerInterface;

class FrequencyService
{
  protected $logger;
  protected $entityManager;
  protected $indicatorRepo;
  protected $taskTemplateRepo;

  public function __construct(
    EntityManagerInterface $entityManager,
    LoggerInterface $logger,
  ) {
    $this->entityManager = $entityManager;
    $this->logger = $logger;

    $this->indicatorRepo = $entityManager->getRepository(Indicator::class);
    $this->taskTemplateRepo = $entityManager->getRepository(TaskTemplate::class);
  }

  public function setNextIteration()
  {
    foreach ($this->indicatorRepo->getFrequencyToUpdate() as $indicator) {
      if ($indicator->getFrequency() !== null)
        $this->persists($this->calculateNextIteration($indicator));
    }

    foreach ($this->taskTemplateRepo->getFrequencyToUpdate() as $taskTemplate) {
      if ($taskTemplate->getFrequency() !== null)
        $this->persists($this->calculateNextIteration($taskTemplate));
    }
  }

  public function calculateNextIteration($entity) {
    $frequency = new Frequency($entity->getFrequency());

    $frequency->calculateNextIteration();
    
    $entity->setFrequency($frequency->jsonSerialize());
    
    return $entity;
  }

  protected function persists($entity) {
    $this->entityManager->persist($entity);
    $this->entityManager->flush();
  }

}