<?php

namespace App\Service;

use App\Entity\RiskManager;

use Doctrine\ORM\EntityManagerInterface;

use Psr\Log\LoggerInterface;

class RiskManagerService extends RogerService
{

  public function __construct(
    EntityManagerInterface $entityManager,
    LoggerInterface $logger,
  ) {
    parent::__construct($entityManager, $logger, RiskManager::class);
  }

  public function newEntity(): RiskManager
  {
    $entity = new RiskManager();

    return $entity;
  }

}
