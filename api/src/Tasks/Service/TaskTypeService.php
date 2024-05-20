<?php

namespace App\Tasks\Service;

use App\Tasks\Entity\TaskType;
use App\Tasks\Entity\TaskWorkflow;

use Beerfranz\RogerBundle\Service\RogerService;

use Doctrine\ORM\EntityManagerInterface;

use Psr\Log\LoggerInterface;

class TaskTypeService extends RogerService
{

	protected $taskWorkflowRepo;

	public function __construct(
		EntityManagerInterface $entityManager,
		LoggerInterface $logger,
	) {
		parent::__construct($entityManager, $logger, TaskType::class);

		$this->taskWorkflowRepo = $entityManager->getRepository(TaskWorkflow::class);
	}

	public function newEntity(): TaskType
	{
		$entity = new TaskType();

		return $entity;
	}

	public function findOneTaskWorkflowByIdentifier(?string $identifier): TaskWorkflow|null
	{
		return $this->taskWorkflowRepo->findOneByIdentifier($identifier);
	}

}
