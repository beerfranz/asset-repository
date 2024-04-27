<?php

namespace App\Tests\Api;

use App\Tests\Api\Functional;
// use App\Tests\Factory\AssetFactory;

use App\Message\IndicatorValueMessage;

class IndicatorTest extends Functional
{

  public static function getIndicatorInput() {
    return [
      'description' => 'test description',
      'namespace' => 'test namespace',
      'targetValue' => 80,
      'triggers' => [
        'danger' => 'value < 60',
        'warning' => 'value < 70',
      ],
      'frequency' => [
        'description' => 'monthly',
        'crontab' => '0 0 1 * * ',
      ]
    ];
  }

  public static function getTaskWorkflow() {
    return [
      'identifier' => 'indicator',
      'statuses' => [
        'todo' => [
          'isDefault' => true,
          'nextStatuses' => [
            'to_valid',
          ]
        ],
        'to_valid' => [
          'constraints' => [
            'attributes.indicatorValue.value is defined',
          ],
          'nextStatuses' => [
            'validated',
          ],
        ],
        'validated' => [
          'isDone' => true,
        ]
      ]
    ];
  }

  public static function getTaskType() {
    return [
      'identifier' => 'indicator',
      'workflowIdentifier' => 'indicator',
    ];
  }

  public static function getTaskTemplate() {
    return [
      'identifier' => 'indicator',
      'title' => 'Indicators',
      'typeIdentifier' => 'indicator',
    ];
  }

  public static function getIndicator() {
    return [
      'identifier' => 'test',
      'description' => 'active users / total users',
      'namespace' => 'application usages',
      'targetValue' => 80,
      'triggers' => [
        'danger' => 'value < 60',
        'warning' => 'value < 70',
      ],
      'frequency' => [
        'description' => 'monthly',
        'crontab' => '0 0 1 * *',
      ],
      'taskTemplate' => '/task_templates/indicator',
    ];
  }

  public static function getIndicatorValue() {
    return [
      'identifier' => '2024-05',
      'value' => 69,
    ];
  }

  public static function getTask() {
    return [
      'identifier' => 'indicator_test_2024-05',
      'title' => 'Indicators',
      'attributes' => [
        'relatedTo' => [
          'indicatorValue' => '/indicators/test/values/2024-05',
        ]
      ],
    ];
  }

  /**
   * Create, read, update, and delete an asset
   **/
  public function testAdminCRUDIndicator(): void
  {

    // Create indicator
    $identifier = 'UnitTest';
    $input = self::getIndicatorInput();

    $output = $this->calculateSimpleOutput('Indicator', $identifier, '/indicators/' . $identifier, $input);

    $this->testIdempotentCrud('/indicators/' . $identifier, [
      'headers' => $this->getAdminUser(),
      'input' => $input,
      'output' => $output,
    ]);

  }

  public function testIndicatorWorkflow(): void
  {
    $this->assertQueueIsEmpty();

    $taskWorkflow = self::getTaskWorkflow();
    $this->testPut('/task_workflows/' . $taskWorkflow['identifier'], [
      'headers' => $this->getAdminUser(),
      'input' => array_diff_key($taskWorkflow, array_flip([ 'identifier' ])),
      'output' => $taskWorkflow,
    ]);

    $taskType = self::getTaskType();
    $this->testPut('/task_types/' . $taskType['identifier'], [
      'headers' => $this->getAdminUser(),
      'input' => array_diff_key($taskType, array_flip([ 'identifier' ])),
      'output' => $taskType,
    ]);

    $taskTemplate = self::getTaskTemplate();
    $this->testPut('/task_templates/' . $taskTemplate['identifier'], [
      'headers' => $this->getAdminUser(),
      'input' => array_diff_key($taskTemplate, array_flip([ 'identifier' ])),
      'output' => $taskTemplate,
    ]);

    $indicator = self::getIndicator();
    $this->testPut('/indicators/' . $indicator['identifier'], [
      'headers' => $this->getAdminUser(),
      'input' => array_diff_key($indicator, array_flip([ 'identifier' ])),
      'output' => $indicator,
    ]);

    $indicatorValue = self::getIndicatorValue();
    $this->testPut('/indicators/' . $indicator['identifier'] . '/values/' . $indicatorValue['identifier'], [
      'headers' => $this->getAdminUser(),
      'input' => array_diff_key($indicatorValue, array_flip([ 'identifier' ])),
      'output' => $indicatorValue,
    ]);

    $this->assertQueueCount(1);
    $this->processQueue();

    $task = self::getTask();
      
    $this->testGet('/tasks/' . $task['identifier'], [
      'headers' => $this->getAdminUser(),
      'output' => $task,
    ]);

    // Test bad status
    $this->testPatch('/tasks/' . $task['identifier'], [
      'headers' => $this->getAdminUser(),
      'input' => [ 'status' => 'validated' ],
      'output' => [],
      'responseStatus' => 400,
    ]);

    // Test status without all constraints
    $this->testPatch('/tasks/' . $task['identifier'], [
      'headers' => $this->getAdminUser(),
      'input' => [ 'status' => 'to_valid' ],
      'output' => [],
      'responseStatus' => 400,
    ]);

    // Update task with value
    $this->testPatch('/tasks/' . $task['identifier'], [
      'headers' => $this->getAdminUser(),
      'input' => [ 'attributes' => [ 'indicatorValue' => [ 'value' => 69 ] ] ],
      'output' => $task,
    ]);

    // Update task status to_valid (should be merged with previous test)
    $this->testPatch('/tasks/' . $task['identifier'], [
      'headers' => $this->getAdminUser(),
      'input' => [ 'status' => 'to_valid' ],
      'output' => array_merge($task, [ 'status' => 'to_valid' ]),
    ]);

    $this->processQueue();

    // Update task status to validated
    $this->testPatch('/tasks/' . $task['identifier'], [
      'headers' => $this->getAdminUser(),
      'input' => [ 'status' => 'validated' ],
      'output' => array_merge($task, [ 'status' => 'validated', 'isDone' => true ]),
    ]);

    $this->processQueue();

    // assert indicator value is validated
    $this->testGet('/indicators/' . $indicator['identifier'] . '/values/' . $indicatorValue['identifier'], [
      'headers' => $this->getAdminUser(),
      'output' => array_merge($indicatorValue, [ 'isValidated' => true ]),
    ]);

  }

}
