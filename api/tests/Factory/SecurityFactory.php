<?php

namespace App\Tests\Factory;

use App\Tests\Factory\RogerFactory;

final class SecurityFactory extends RogerFactory
{
	public static function getPath(string $target): string
	{
		if ($target === 'authorizations')
			return '/admin/authorizations';
		if ($target === 'authorization_namespaces')
			return '/admin/authorization_namespaces';
		if ($target === 'authorization_policies')
			return '/admin/authorization_policies';
		if ($target === 'authorization_roles')
			return '/admin/authorization_roles';
		if ($target === 'user_groups')
			return '/admin/user_groups';
		if ($target === 'users')
			return '/admin/users';

	}

	public static function getUser($options = [])
	{
		$e = [
			'subject' => self::randomString(),
			'email' => self::randomString(),
		];

		if (isset($options['groups']))
			$e['groups'] = $options['groups'];

		return $e;
	}

	public static function getGroup($options = [])
	{
		$e = [
			'identifier' => self::randomString(),
		];

		return $e;
	}

	public static function getAutorizationPolicy($options = [])
	{
		if (!isset($options['namespace']))
			$options['namespace'] = 'task';

		if (!isset($options['relation']))
			$options['relation'] = 'task:read';

		$e = [
			'identifier' => self::randomString(),
			'namespace' => $options['namespace'],
			'object' => '*',
			'relation' => $options['relation'],
		];

		if (isset($options['groups']))
			$e['groups'] = $options['groups'];

		return $e;
	}

}
