<?php

namespace App\Security\State;

use App\Security\ApiResource\AuthorizationNamespace;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;

final class AuthorizationNamespaceState implements ProviderInterface
{
	public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
	{
		$namespaces = [
			'tasks' => '/task_authorizations',
			'assessments' => '/assessments/authorizations',
		];

		$out = [];

		foreach ($namespaces as $namespace => $link) {
			$out[] = new AuthorizationNamespace([ 'namespace' => $namespace, 'link' => $link ]);
		}

		return $out;
	}
}
