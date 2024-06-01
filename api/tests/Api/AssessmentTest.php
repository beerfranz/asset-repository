<?php

namespace App\Tests\Api;

use App\Tests\Api\Functional;

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

		

		// Create assessment template
		$identifier = 'unitTest';
		$input = self::getAssessmentTemplateInput();
		$input['assets'] = [ $assetIdentifier_1, $assetIdentifier_2 ];
		
		$output = $this->calculateSimpleOutput('Template', $identifier, '/assessments/templates/' . $identifier, $input);

		$this->testIdempotentCrud('/assessments/templates/' . $identifier, [
			'headers' => $this->getAdminUser(),
			'input' => $input,
			'output' => $output,
		]);
		

		// Generate plan from template


		// Process tasks

	}


}
