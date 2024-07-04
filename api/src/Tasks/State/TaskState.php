<?php

namespace App\Tasks\State;

use App\Tasks\ApiResource\Task as TaskApi;
use App\Tasks\ApiResource\TaskType;
use App\Tasks\ApiResource\TaskTemplate;
use App\Tasks\Entity\Task as TaskEntity;
use App\Tasks\Service\TaskService;

use Beerfranz\RogerBundle\State\RogerState;
use Beerfranz\RogerBundle\State\RogerStateFacade;

use ApiPlatform\Metadata\Operation;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class TaskState extends RogerState
{

	public function __construct(
		RogerStateFacade $facade,
		TaskService $service,
	) {
		parent::__construct($facade, $service);
	}

	public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
	{
		if (!isset($context['filters']['status']))
			$context['filters']['isDone'] = false;
		return $this->stateProvide($operation, $uriVariables, $context);
	}

	public function newApi(): TaskApi
	{
		return new TaskApi();
	}

	public function fromApiToEntity($api, $entity): TaskEntity
	{
		if ($entity->getIdentifier() === null) {
			$entity->setIdentifier($api->__get('identifier'));
			$entity->setCreatedAt(new \DateTimeImmutable());
		}

		$entity->setTitle($api->__get('title'));
		$entity->setDescription($api->__get('description'));
		$entity->setAssignedTo($api->__get('assignedTo'));
		$entity->setOwner($api->__get('owner'));

		if ($this->isPatch && null !== $entity->getAttributes())
			$entity->setAttributes(array_replace_recursive($entity->getAttributes() ,$api->__get('attributes')));
		else
			$entity->setAttributes($api->__get('attributes'));

		$taskTemplate = $this->service->findOneTaskTemplateByIdentifier($api->__get('taskTemplateIdentifier'));
		$this->service->setTaskTemplate($entity, $taskTemplate);

		$status = $api->__get('status');
		$entityStatus = $entity->getStatus();

		if ($status !== null && $this->service->askNewStatus($entity, $status))
			$entity->setStatus($api->__get('status'));
		
		if ($entity->getStatus() === null && $entity->getTaskTemplate() !== null) {
			$workflowDefaultStatus = $this->service->getDefaultStatus($entity->getTaskTemplate());
			if ($workflowDefaultStatus !== null)
				$entity->setStatus($this->service->getDefaultStatus($entity->getTaskTemplate()));
		}

		foreach ($api->tags as $name => $opts) {
			$entity->addTag($this->service->getTag($name, $opts));
		}

		return $entity;
	}

	public function fromEntityToApi($entity, $api): TaskApi
	{
		// $this->simpleFromEntityToApi($entity, $api);
		$api->identifier = $entity->getIdentifier();
		$api->title = $entity->getTitle();
		$api->description = $entity->getDescription();
		$api->status = $entity->getStatus();
		$api->isDone = $entity->isIsDone();
		$api->createdAt = $entity->getCreatedAt();
		$api->owner = $entity->getOwner();
		$api->assignedTo = $entity->getAssignedTo();
		$api->attributes = $entity->getAttributes();
		$taskTemplate = $entity->getTaskTemplate();
		if (null !== $taskTemplate) {
			$api->taskTemplate = new TaskTemplate([ 'identifier' => $entity->getTaskTemplate()->getIdentifier() ]);
		}
		$taskType =  $entity->getTaskType();
		if (null !== $taskType)
			$api->taskType = new TaskType([ 'identifier' => $taskType->getIdentifier() ]);

		$tags = [];
		try {
			foreach ($entity->getTags() as $tag) {
				$tags[$tag->getName()] = [ 'value' => $tag->getValue(), 'color' => $tag->getColor() ];
			}
		} catch(\Error $e) {}
		$api->__set('tags', $tags);

		$api->__set('allowedNextStatuses', $this->service->possibleNextStatus($entity));

		return $api;
	}
}
