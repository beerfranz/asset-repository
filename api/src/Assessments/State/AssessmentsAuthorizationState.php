<?php

namespace App\Assessments\State;

use App\Assessments\ApiResource\Authorization;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;

final class AssessmentsAuthorizationState implements ProviderInterface
{
	public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
	{
		$relations = [
			'Assessments:admin',
		];

		$authorizations = [];

		foreach ($relations as $relation) {
			$authorizations[] = new Authorization([ 'relation' => $relation ]);
		}

		return $authorizations;
	}
}
