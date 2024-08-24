<?php

namespace App\Tests\Factory;

use App\Tests\Factory\RogerFactory;

final class TaslFactory extends RogerFactory
{

	protected function getTaskTemplate(array $options = []): array
	{

	}

	protected function getTaskType(array $options = []): array
	{
		$e = [];

		if (isset($options['workflowIdentifier']))
			$e['workflowIdentifier'] = $options['workflowIdentifier'];

		return $e;
	}

	protected function getTaskWorkflow(array $options = []): array
	{
		$e = [];

		$e['statuses'] = [
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
		];
			
		return $e;
	}

}
