<?php

namespace App\Entity;

use Beerfranz\RogerBundle\Entity\RogerEntity;

use Symfony\Component\Scheduler\Trigger\CronExpressionTrigger;

use ApiPlatform\Metadata\ApiResource;

class Frequency extends RogerEntity {

  protected string $description;

  protected ?string $crontab = null;

  protected \DateTimeImmutable $startsAt;

  protected ?\DateTimeImmutable $endsAt;

  protected \DateTimeImmutable $nextIterationAt;

  public function __construct(array $data = []) {

    if (!isset($data['startsAt']))
      $data['startsAt'] = new \DateTimeImmutable();

    $this->hydrator($data);
  }

  public function calculateNextIteration()
  {
    $cronExpression = CronExpressionTrigger::fromSpec($this->crontab);

    $this->nextIterationAt = $cronExpression->getNextRunDate(new \DateTimeImmutable);
  }
}
