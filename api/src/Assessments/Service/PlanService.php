<?php

namespace App\Assessments\Service;

use App\Assessments\Entity\AssessmentPlan;

use App\Tasks\Entity\Task;
use App\Entity\Asset;

use Beerfranz\RogerBundle\Service\RogerService;

use Doctrine\ORM\EntityManagerInterface;

use Psr\Log\LoggerInterface;

class PlanService extends RogerService
{

	protected $assetRepo;
	protected $taskRepo;

	public function __construct(
		EntityManagerInterface $entityManager,
		LoggerInterface $logger,
	) {
		parent::__construct($entityManager, $logger, AssessmentPlan::class);

		$this->assetRepo = $entityManager->getRepository(Asset::class);
		$this->taskRepo = $entityManager->getRepository(Task::class);

	}

	public function newEntity(): AssessmentPlan
	{
		$entity = new AssessmentPlan();

		return $entity;
	}

	public function getPlans(TaskTemplate $taskTemplate, $assetIdentifier, $type = 'assessment')
	{
		$asset = $this->assetRepo->findOneByIdentifier($assetIdentifier);

		if ($asset === null)
			throw new \Exception('Asset ' . $assetIdentifier . ' not exists.');

		$plan = $this->repo->findOneByIdentifiers($taskTemplate, $asset, $type);

		if ($plan === null)
			return new AssessmentPlan(['asset' => $asset, 'taskTemplate' => $taskTemplate, 'type' => $type]);
		else
			return $plan;
	}

	public function findOneAssetByIdentifier(string $identifier): ?Asset
	{
		return $this->assetRepo->findOneByIdentifier($identifier);
	}

	public function findOneTaskByIdentifier(string $identifier): ?Task
	{
		return $this->taskRepo->findOneByIdentifier($identifier);
	}

}
