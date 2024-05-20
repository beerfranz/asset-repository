<?php

namespace App\Tasks\Service;

use App\Tasks\Entity\Task;
use App\Tasks\Entity\TaskTemplate;
use App\Tasks\Entity\TaskWorkflow;
use App\Tasks\Entity\TaskWorkflowStatus;

use Beerfranz\RogerBundle\Service\RogerService;

use Doctrine\ORM\EntityManagerInterface;

use Psr\Log\LoggerInterface;

class TaskWorkflowService extends RogerService
{

	public function __construct(
		EntityManagerInterface $entityManager,
		LoggerInterface $logger,
	) {
		parent::__construct($entityManager, $logger, TaskWorkflow::class);
	}

	public function newEntity(): TaskWorkflow
	{
		$taskWorkflow = new TaskWorkflow();

		return $taskWorkflow;
	}

}