<?php

namespace App\Security\State;

use App\Security\ApiResource\AuthorizationRole;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;

final class AuthorizationRoleState implements ProviderInterface
{
	public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
	{
		$roles = [
			'ROLE_SUPER_ADMIN' => [
				'label' => 'admin of all tenants',
				'description' => 'Can manage authorization',
			],
			'ROLE_ADMIN' => [
				'label' => 'admin',
				'description' => 'Can manage authorization',
			],
			'ROLE_USER' => [
				'label' => 'user',
				'description' => 'Can consume API',
			],
		];

		$out = [];

		foreach ($roles as $identifier => $attributes) {
			$out[] = new AuthorizationRole(array_merge([ 'identifier' => $identifier ], $attributes));
		}

		return $out;
	}
}
