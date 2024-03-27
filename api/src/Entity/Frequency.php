<?php

namespace App\Entity;

use App\Entity\RogerEntity;

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
}
