<?php

namespace App\Tasks\State;

use App\Tasks\ApiResource\TaskAuthorization;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;

final class TaskAuthorizationState implements ProviderInterface
{
	public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
	{
		$relations = [
			'TaskType:read',
			'TaskTemplate:read',
		];

		$authorizations = [];

		foreach ($relations as $relation) {
			$authorizations[] = new TaskAuthorization([ 'relation' => $relation ]);
		}

		return $authorizations;
	}
}
