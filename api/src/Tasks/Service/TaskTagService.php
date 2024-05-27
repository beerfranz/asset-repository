<?php

namespace App\Tasks\Service;

use App\Tasks\Entity\TaskTag;
use App\Tasks\Entity\TaskWorkflow;

use Beerfranz\RogerBundle\Service\RogerService;

use Doctrine\ORM\EntityManagerInterface;

use Psr\Log\LoggerInterface;

class TaskTagService extends RogerService
{

	protected $taskWorkflowRepo;

	public function __construct(
		EntityManagerInterface $entityManager,
		LoggerInterface $logger,
	) {
		parent::__construct($entityManager, $logger, TaskTag::class);
	}

	public function newEntity(): TaskTag
	{
		$entity = new TaskTag();

		return $entity;
	}

	public function getTag($name, $opts = []): TaskTag
	{
		if (!isset($opts['value']))
			$opts['value'] = null;

		if (!isset($opts['color']))
			$opts['color'] = null;

		$taskTag = $this->repo->findOneBy(['name' => $name, 'value' => $opts['value']]);
		if ($taskTag === null)
			return new TaskTag(['name' => $name, 'value' => $opts['value'], 'color' => $opts['color']]);
		else
			return $taskTag;
		

	}

}
