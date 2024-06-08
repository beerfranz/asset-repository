<?php

namespace App\Indicators\Service;

use App\Indicators\Entity\Indicator;
use App\Indicators\Entity\IndicatorValue;
use Beerfranz\RogerBundle\Entity\RogerEntityInterface;
use Beerfranz\RogerBundle\Service\RogerService;
use App\Indicators\Message\IndicatorValueMessage;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;

use Psr\Log\LoggerInterface;

class IndicatorValueService extends RogerService
{
	protected $indicatorRepo;

	public function __construct(
		EntityManagerInterface $entityManager,
		LoggerInterface $logger,
		protected MessageBusInterface $bus,
		protected Security $security,
	) {
		parent::__construct($entityManager, $logger, IndicatorValue::class);

		$this->indicatorRepo = $entityManager->getRepository(Indicator::class);
	}

	public function newEntity(): IndicatorValue
	{
		$entity = new IndicatorValue();

		return $entity;
	}

	public function validate(IndicatorValue $indicatorValue): IndicatorValue
	{
		if ($indicatorValue->getValidator() === null)
			$indicatorValue->setValidator($this->security->getUser()->getEmail());

		$indicatorValue->setIsValidated(true);

		return $indicatorValue;
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

	public function persistEntity(RogerEntityInterface $entity)
	{
		parent::persistEntity($entity);
	}
}
