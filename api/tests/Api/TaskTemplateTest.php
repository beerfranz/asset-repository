<?php

namespace App\Tests\Api;

use App\Tests\Api\Functional;
use App\Tests\Factory\TaskTemplateFactory;

class TaskTemplateTest extends Functional
{

  public static function getWorkflowInput() {
    return [
      'statuses' => [
        'toCheck' => [
          'isDefault' => true,
          'nextStatuses' => [ 'checked', 'failed', 'skip' ],
        ],
        'checked' => [
          'isDone' => true,
        ],
        'failed' => [
          'isDone' => true,
        ],
        'skip' => [
          'isDone' => true,
        ],
      ]
    ];
  }

  public function testAdminCreateTaskTemplate(): void
  {
    $identifier = 'tt-001';
    $input = [
      'title' => 'test title',
      'description' => 'test description',
    ];

    $output = array_merge($input, [
      '@context' => '/contexts/TaskTemplate',
      '@id' => '/task_templates/' . $identifier,
      '@type' => 'TaskTemplate',
      'identifier' => $identifier,
    ]);

    $this->testIdempotentCrud('/task_templates/' . $identifier, [
      'headers' => $this->getAdminUser(),
      'input' => $input,
      'output' => $output,
    ]);

  }

  protected function generateTaskFromTaskTemplate($uri, $context)
  {
    static::createClient()->request('PUT', $uri, [
      'json' => [], 
      'headers' => $context['headers'],
    ]);

    $this->assertResponseStatusCodeSame(200);

    $this->testGet($context['taskUri'], [
      'headers' => $context['headers'],
      'output' => $context['taskOutput'],
    ]);
  }

  public function testAdminCreateTaskTemplateAndPropagate(): void
  {
    $identifier = 'tt-001';
    $input = [
      'title' => 'test title',
      'description' => 'test description',
      'tags' => [ 'priority' => [ 'value' => 'low', 'color' => 'black' ]],
    ];

    $output = array_merge($input, [
      '@context' => '/contexts/TaskTemplate',
      '@id' => '/task_templates/' . $identifier,
      '@type' => 'TaskTemplate',
      'identifier' => $identifier,
    ]);

    $taskIdentifier = $identifier . '_' . 't-001';
    $taskOutput = array_merge($input, [
      '@context' => '/contexts/Task',
      '@id' => '/tasks/' . $taskIdentifier,
      '@type' => 'Task',
      'identifier' => $taskIdentifier,
    ]);

    $this->testPut('/task_templates/' . $identifier, [
      'headers' => $this->getAdminUser(),
      'input' => $input,
      'output' => $output,
    ]);

    $this->generateTaskFromTaskTemplate('/task_templates/' . $identifier . '/generate/t-001', [
      'headers' => $this->getAdminUser(),
      'taskUri' => '/tasks/' . $taskIdentifier,
      'taskOutput' => $taskOutput
    ]);

  }

  public function testTaskTemplateWithWorkflow(): void
  {

    // Add workflow

    $workflowIdentifier = 'workflow-QA';
    $workflowInput = self::getWorkflowInput();
    $workflowOutput = $this->calculateSimpleOutput('TaskWorkflow', $workflowIdentifier, '/task_workflows/' . $workflowIdentifier, $workflowInput);

    $this->testPut('/task_workflows/' . $workflowIdentifier, [
      'headers' => $this->getAdminUser(),
      'input' => $workflowInput,
      'output' => $workflowOutput,
    ]);

    // Add type with workflow

    $typeIdentifier = 'type-QA';
    $typeInput = [
      'workflowIdentifier' => $workflowIdentifier,
    ];
    $typeOutput = $this->calculateSimpleOutput('TaskType', $typeIdentifier, '/task_types/' . $typeIdentifier, $typeInput);

    $this->testPut('/task_types/' . $typeIdentifier, [
      'headers' => $this->getAdminUser(),
      'input' => $typeInput,
      'output' => $typeOutput,
    ]);

    // Add template with type

    $taskTemplateIdentifier = 'tt-001';
    $taskTemplateInput = [
      'title' => 'test title',
      'description' => 'test description',
      'typeIdentifier' => $typeIdentifier,
    ];

    $taskTemplateOutput = $this->calculateSimpleOutput('TaskTemplate', $taskTemplateIdentifier, '/task_templates/' . $taskTemplateIdentifier, $taskTemplateInput);

    $this->testPut('/task_templates/' . $taskTemplateIdentifier, [
      'headers' => $this->getAdminUser(),
      'input' => $taskTemplateInput,
      'output' => $taskTemplateOutput,
    ]);

    // Generate task from template

    $taskIdentifier = $taskTemplateIdentifier . '_' . 't-001';
    $taskOutput = $this->calculateSimpleOutput('Task', $taskIdentifier, '/tasks/' . $taskIdentifier, 
      array_merge($taskTemplateInput, [ 'status' => 'toCheck' ] ));
    unset($taskOutput['typeIdentifier']);

    $this->generateTaskFromTaskTemplate('/task_templates/' . $taskTemplateIdentifier . '/generate/t-001', [
      'headers' => $this->getAdminUser(),
      'taskUri' => '/tasks/' . $taskIdentifier,
      'taskOutput' => $taskOutput
    ]);

    // Update task status

    // test bad status (not in workflow)
    $this->testPatch('/tasks/' . $taskIdentifier, [
      'headers' => $this->getAdminUser(),
      'input' => [ 'status' => 'foo' ],
      'output' => [
        '@type' => 'hydra:Error',
        'hydra:description' => 'status "foo" not allowed in the workflow workflow-QA. Allowed values: checked, failed, skip',
      ],
      'responseStatus' => 400,
    ]);

    // good status with isDone
    $this->testPatch('/tasks/' . $taskIdentifier, [
      'headers' => $this->getAdminUser(),
      'input' => [ 'status' => 'checked' ],
      'output' => [ 'isDone' => true ],
    ]);
  }

}
