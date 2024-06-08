<?php

namespace App\Tests\Api;

use App\Tests\Api\Functional;

use App\Tests\Api\TaskTemplateTest;
use App\Tests\Factory\AssessmentFactory;

class AssessmentTest extends Functional
{

	public static function getAssessmentPlanInput()
	{
		return [
			'title' => 'unit test',
		];
	}

	public static function getAssessmentTemplateInput()
	{
		return [
			'title' => 'unit test',
			'taskTemplates' => [
			],
			'assets' => [
			],
		];
	}

	public static function getAssetInput()
	{
		return [
			
		];
	}

	/**
	 * Create, read, update, and delete a template
	 **/
	public function testAdminCRUDTemplate(): void
	{

		$identifier = 'unitTest';
		$input = self::getAssessmentTemplateInput();

		$output = $this->calculateSimpleOutput('Template', $identifier, '/assessments/templates/' . $identifier, $input);

		$this->testIdempotentCrud('/assessments/templates/' . $identifier, [
			'headers' => $this->getAdminUser(),
			'input' => $input,
			'output' => $output,
		]);

	}

	/**
	 * Create, read, update, and delete a plan
	 **/
	public function testAdminCRUDPlan(): void
	{

		$identifier = 'unitTest';
		$input = self::getAssessmentPlanInput();
		
		$output = $this->calculateSimpleOutput('Plan', $identifier, '/assessments/plans/' . $identifier, $input);

		$this->testIdempotentCrud('/assessments/plans/' . $identifier, [
			'headers' => $this->getAdminUser(),
			'input' => $input,
			'output' => $output,
		]);

	}

	public function testAssessment(): void
	{

		// Create assets

		$assetIdentifier_1 = 'asset-1';
		$data = [
				'identifier' => $assetIdentifier_1,
		];
		static::createClient()->request('POST', '/assets', [
			'json' => $data,
			'headers' => $this->getAdminUser(),
		]);

		$assetIdentifier_2 = 'asset-2';
		$data = [
				'identifier' => $assetIdentifier_2,
		];
		static::createClient()->request('POST', '/assets', [
			'json' => $data,
			'headers' => $this->getAdminUser(),
		]);

		// Create task templates
		// Add workflow
	    $workflowIdentifier = 'workflow-QA';
	    $workflowInput = TaskTemplateTest::getWorkflowInput();
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

	    $taskTemplateIdentifier_2 = 'tt-002';
	    $taskTemplateInput = [
	      'title' => 'test title',
	      'description' => 'test description',
	      'typeIdentifier' => $typeIdentifier,
	    ];
	    $taskTemplateOutput = $this->calculateSimpleOutput('TaskTemplate', $taskTemplateIdentifier_2, '/task_templates/' . $taskTemplateIdentifier_2, $taskTemplateInput);
	    $this->testPut('/task_templates/' . $taskTemplateIdentifier_2, [
	      'headers' => $this->getAdminUser(),
	      'input' => $taskTemplateInput,
	      'output' => $taskTemplateOutput,
	    ]);

		// Create assessment template
		$identifier = 'unitTest';
		$input = self::getAssessmentTemplateInput();
		$input['assets'] = [ $assetIdentifier_1, $assetIdentifier_2 ];
		$input['taskTemplates'] = [$taskTemplateIdentifier, $taskTemplateIdentifier_2];		
		$output = $this->calculateSimpleOutput('Template', $identifier, '/assessments/templates/' . $identifier, $input);
		$this->testPut('/assessments/templates/' . $identifier, [
			'headers' => $this->getAdminUser(),
			'input' => $input,
			'output' => $output,
		]);
		

		// Generate plan from template
		static::createClient()->request('POST', '/assessments/templates/' . $identifier . '/generate-plan',
			[
				'json' => [
					'assets' => [ $assetIdentifier_1 ],
				],
				'headers' => $this->getAdminUser(),
			]
		);

		// Process tasks
		$this->testGetCollection('/tasks', [
			'headers' => $this->getAdminUser(),
			'output' => [
				'hydra:totalItems' => 2,
			],
		]);

	}


}
