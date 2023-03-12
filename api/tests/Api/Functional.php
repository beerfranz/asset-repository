<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

abstract class Functional extends ApiTestCase {
	use ResetDatabase, Factories;

	protected $entityManager;

	protected function setUp(): void
  {
      $kernel = self::bootKernel();

      $this->entityManager = $kernel->getContainer()
          ->get('doctrine')
          ->getManager();

      parent::setUp();
  }

	protected function getAdminUser()
	{
		return [ 'x-token-user-email' => 'unittest-admin@local.com', 'x-token-user-roles' => 'ASSET_ADMIN' ];
	}

	protected function getReadUser()
	{
		return [ 'x-token-user-email' => 'unittest-reader@local.com', 'x-token-user-roles' => 'ASSET_READ' ];
	}

	protected function getWriteUser1()
	{
		return [ 'x-token-user-email' => 'unittest-writer-1@local.com', 'x-token-user-roles' => 'ASSET_WRITE' ];
	}

	protected function getWriteUser2()
	{
		return [ 'x-token-user-email' => 'unittest-writer-2@local.com', 'x-token-user-roles' => 'ASSET_WRITE' ];
	}

	protected function getClientAdmin()
	{
		return static::createClient([], [ 'headers' => $this->getAdminUser() ]);
	}
}
