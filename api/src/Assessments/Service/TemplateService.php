<?php

namespace App\Assessments\Service;

use App\Assessments\Entity\AssessmentTemplate;
use App\Assessments\Entity\AssessmentPlan;
use App\Tasks\Entity\TaskTemplate;
use App\Tasks\Service\TaskService;
use App\Assets\Entity\Asset;

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
		protected TaskService $taskService,
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

	public function generatePlanFromTemplate(AssessmentTemplate $template, string $assetIdentifier): AssessmentPlan
	{
		$asset = $this->findOneAssetByIdentifier($assetIdentifier);

		if ($asset === null)
			throw new \Exception('Cannot generate plan without asset (asset identifier: ' . $assetIdentifier .').');

		$plan = new AssessmentPlan([
			// 'identifier' => $identifier,
			'title' => $template->getTitle(),
		]);

		$plan->setAsset($asset);
		$plan->setAssessmentTemplate($template);

		$this->persistEntity($plan);

		return $plan;
		
	}

}
