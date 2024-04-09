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

	protected function testGetCollection(string $uri, array $context)
	{
		$this->testGet($uri, $context);
	}

	protected function testPost(string $uri, array $context)
	{
		static::createClient()->request('POST', $uri,
      [
      	'json' => $context['input'],
      	'headers' => $context['headers'],
    	]
  	);
    $this->assertResponseStatusCodeSame(200);
    $this->assertJsonContains($context['output']);
	}

	protected function testGet(string $uri, array $context) {
		static::createClient()->request('GET', $uri,
      [ 'headers' => $context['headers'] ]);

    $this->assertResponseStatusCodeSame(200);
    $this->assertJsonContains($context['output']);
	}

	protected function testDelete(string $uri, array $context) {
		static::createClient()->request('DELETE', $uri,
      [ 'headers' => $context['headers']
    ]);
    $this->assertResponseStatusCodeSame(204);

    $this->assertNotExists($uri, $context);
	}

	protected function testPut(string $uri, array $context) {
		static::createClient()->request('PUT', $uri,
      [
      	'json' => $context['input'],
      	'headers' => $context['headers'],
    	]
  	);
    $this->assertResponseStatusCodeSame(200);
    $this->assertJsonContains($context['output']);

    if (isset($context['withGetTest']) && $context['withGetTest'] === true)
    	$this->testGet($uri, $context);
	}

	protected function testIdempotentCrud(string $uri, array $context) {
		$this->assertNotExists($uri, $context);
		$this->testPut($uri, array_merge($context, ['withGetTest' => true ]));
		$this->testDelete($uri, $context);
	}

	protected function calculateSimpleOutput(string $class, string $identifier, string $uri, array $input = []): array
	{
		return array_merge($input, [
      '@context' => '/contexts/'. $class,
      '@id' => $uri,
      '@type' => $class,
      'identifier' => $identifier,
    ]);
	}
}
