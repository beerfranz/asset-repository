<?php

namespace App\Tests\Api;

use App\Tests\Api\RogerTestMessageTrait;
use App\Tests\Api\RogerTestApiTrait;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

abstract class Functional extends ApiTestCase {
	use ResetDatabase, Factories, RogerTestMessageTrait, RogerTestApiTrait;

	protected $entityManager;

	protected $response;

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
		return [ 'x-token-user-email' => 'unittest-admin@local.com', 'x-token-user-roles' => 'ROLE_ASSET_ADMIN' ];
	}

	protected function getReadUser()
	{
		return [ 'x-token-user-email' => 'unittest-reader@local.com', 'x-token-user-roles' => 'ROLE_ASSET_READ' ];
	}

	protected function getWriteUser1()
	{
		return [ 'x-token-user-email' => 'unittest-writer-1@local.com', 'x-token-user-roles' => 'ROLE_ASSET_WRITE' ];
	}

	protected function getWriteUser2()
	{
		return [ 'x-token-user-email' => 'unittest-writer-2@local.com', 'x-token-user-roles' => 'ROLE_ASSET_WRITE' ];
	}

	protected function getClientAdmin()
	{
		return static::createClient([], [ 'headers' => $this->getAdminUser() ]);
	}

	protected function assertNotExists(string $uri, array $context)
	{
		static::createClient()->request(
			'GET',
			$uri,
      [ 'headers' => $context['headers'] ]
    );
    $this->assertResponseStatusCodeSame(404);
	}

}
