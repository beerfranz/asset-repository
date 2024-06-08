<?php

namespace App\Tasks\Service;

use App\Tasks\Entity\Task;
use App\Tasks\Entity\TaskTemplate;
use App\Tasks\Entity\TaskWorkflow;
use App\Tasks\Entity\TaskType;

use App\Tasks\Service\TaskService;
use App\Tasks\Service\TaskTagService;

use Beerfranz\RogerBundle\Service\RogerService;

use Doctrine\ORM\EntityManagerInterface;

use Psr\Log\LoggerInterface;

class TaskTemplateService extends RogerService
{

	protected $taskTypeRepo;

	public function __construct(
		EntityManagerInterface $entityManager,
		LoggerInterface $logger,
		protected TaskService $taskService,
		protected TaskTagService $taskTagService,
	) {
		parent::__construct($entityManager, $logger, TaskTemplate::class);

		$this->taskTypeRepo = $entityManager->getRepository(TaskType::class);
	}

	public function newEntity(): TaskTemplate
	{
		$taskTemplate = new TaskTemplate();

		return $taskTemplate;
	}

	public function findOneTaskTypeByIdentifier($identifier)
	{
		return $this->taskTypeRepo->findOneByIdentifier($identifier);
	}

	public function generateTaskFromTaskTemplate(TaskTemplate $taskTemplate, $identifier): Task
	{
		return $this->taskService->generateTaskFromTaskTemplate($taskTemplate, $identifier);
	}

	public function getTag($name, $opts = [])
	{
		return $this->taskTagService->getTag($name, $opts);
	}

}
