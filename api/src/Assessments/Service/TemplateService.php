<?php

namespace App\Assessments\Service;

use App\Assessments\Entity\AssessmentTemplate;

use App\Tasks\Entity\TaskTemplate;
use App\Entity\Asset;

use Beerfranz\RogerBundle\Service\RogerService;

use Doctrine\ORM\EntityManagerInterface;

use Psr\Log\LoggerInterface;

class TemplateService extends RogerService
{

	protected $assetRepo;
	protected $taskTemplateRepo;

	public function __construct(
		EntityManagerInterface $entityManager,
		LoggerInterface $logger,
	) {
		parent::__construct($entityManager, $logger, AssessmentTemplate::class);

		$this->assetRepo = $entityManager->getRepository(Asset::class);
		$this->taskTemplateRepo = $entityManager->getRepository(TaskTemplate::class);

	}

	public function newEntity(): AssessmentTemplate
	{
		$entity = new AssessmentTemplate();

		return $entity;
	}

	public function findOneAssetByIdentifier(string $identifier): ?Asset
	{
		return $this->assetRepo->findOneByIdentifier($identifier);
	}

	public function findOneTaskTemplateByIdentifier(string $identifier): ?TaskTemplate
	{
		return $this->taskTemplateRepo->findOneByIdentifier($identifier);
	}

}
