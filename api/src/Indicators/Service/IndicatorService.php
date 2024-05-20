<?php

namespace App\Indicators\Service;

use App\Indicators\Entity\Indicator;
use App\Indicators\Entity\IndicatorValue;

use Beerfranz\RogerBundle\Service\RogerService;

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
}
