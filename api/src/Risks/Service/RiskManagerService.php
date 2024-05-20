<?php

namespace App\Risks\Service;

use App\Risks\Entity\RiskManager;

use Beerfranz\RogerBundle\Service\RogerService;

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
