<?php

namespace App\Tasks\Controller;

use App\Service\AuditService;
use App\Tasks\Service\TaskService;
use App\Tasks\Service\TaskTypeService;
use App\Repository\TaskTemplateRepository;
use App\Repository\TaskWorkflowRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class TasksController extends AbstractController
{

	#[Route('/ui/tasks', name: 'get_tasks', methods: ['GET'])]
	public function getTasks(Request $request): Response
	{
		return $this->render('tasks.html.twig', [ 'navbar' => [ 'tasks' => 'active' ] ]);
	}


	#[Route('/ui/tasks/{identifier}', name: 'getTask', methods: ['GET'])]
	public function getTask(string $identifier, TaskService $service, AuditService $auditService, Request $request): Response
	{
		$task = $service->findOneByIdentifier($identifier);
		$allowedNextStatuses = $service->possibleNextStatus($task);

		$audits = $auditService->find('Task', $task->getIdentifier());

		return $this->render('task.html.twig', [ 'task' => $task, 'allowedNextStatuses' => $allowedNextStatuses, 'audits' => $audits ]);
	}

	#[Route('/ui/task-templates', name: 'getTaskTemplates', methods: ['GET'])]
	public function getTaskTemplates(Request $request): Response
	{
		return $this->render('task-templates.html.twig', [ 'navbar' => [ 'task_templates' => 'active' ] ]);
	}

	#[Route('/ui/task-templates/{identifier}', name: 'getTaskTemplate', methods: ['GET'])]
	public function getTaskTemplate(string $identifier, TaskTemplateRepository $repo, Request $request): Response
	{
		$taskTemplate = $repo->findOneByIdentifier($identifier);

		return $this->render('task-template.html.twig', [ 'taskTemplate' => $taskTemplate ]);
	}

	#[Route('/ui/task-types', name: 'getTaskTypes', methods: ['GET'])]
	public function getTaskTypes(Request $request): Response
	{
		return $this->render('task-types.html.twig', [ 'navbar' => [ 'task-types' => 'active' ] ]);
	}

	#[Route('/ui/task-types/{identifier}', name: 'getTaskType', methods: ['GET'])]
	public function getTaskType(string $identifier, TaskTypeService $service, Request $request): Response
	{
		$taskType = $service->findOneByIdentifier($identifier);

		return $this->render('task-type.html.twig', [ 'taskType' => $taskType ]);
	}

	#[Route('/ui/task-workflows', name: 'getTaskWorkflows', methods: ['GET'])]
	public function getTaskWorkflows(Request $request): Response
	{
		return $this->render('task-workflows.html.twig', [ 'navbar' => [ 'task-workflows' => 'active' ] ]);
	}

	#[Route('/ui/task-workflows/{identifier}', name: 'getTaskWorkflow', methods: ['GET'])]
	public function getTaskWorkflow(string $identifier, TaskWorkflowRepository $repo, Request $request): Response
	{
		$taskWorkflow = $repo->findOneByIdentifier($identifier);

		return $this->render('task-workflow.html.twig', [ 'taskWorkflow' => $taskWorkflow ]);
	}

}
