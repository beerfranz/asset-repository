<?php

namespace App\Tests\Factory;

use App\Tests\Factory\RogerFactory;

final class SecurityFactory extends RogerFactory
{

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

}
