<?php

namespace App\Tests\Api;

use App\Tests\Api\Functional;
use App\Tests\Factory\SecurityFactory;

class SecurityTest extends Functional
{

	public function testCrudUser(): void
	{
		$user = SecurityFactory::getUser();
		
		$output = $this->calculateSimpleOutput('User', null, null, $user);

		$this->testCrud(SecurityFactory::getPath('users'), [
			'headers' => $this->getAdminUser(),
			'input' => $user,
			'output' => $output,
		]);
	}

	public function testCrudGroup(): void
	{
		$group = SecurityFactory::getGroup();
		
		$output = $this->calculateSimpleOutput('UserGroup', null, null, $group);

		$this->testCrud(SecurityFactory::getPath('user_groups'), [
			'headers' => $this->getAdminUser(),
			'input' => $group,
			'output' => $output,
		]);

	}

	public function testCrudAuthorizationPolicy(): void
	{
		$policy = SecurityFactory::getAutorizationPolicy();
		
		$output = $this->calculateSimpleOutput('AuthorizationPolicy', null, null, $policy);

		$this->testCrud(SecurityFactory::getPath('authorization_policies'), [
			'headers' => $this->getAdminUser(),
			'input' => $policy,
			'output' => $output,
		]);
	}

	public function testRoleList(): void
	{
		$output = [
			'hydra:member' => [
				[ 'identifier' => 'ROLE_ADMIN' ],
				[ 'identifier' => 'ROLE_USER' ],
			]
			
		];
		$this->testGetCollection(SecurityFactory::getPath('authorization_roles'), [
			'headers' => $this->getAdminUser(),
			'output' => $output,
		]);
	}

	public function testNamespaceList(): void
	{
		$output = [
			'hydra:member' => [
				[ 'namespace' => 'tasks' ],
				[ 'namespace' => 'assessments' ],
			]
			
		];
		$this->testGetCollection(SecurityFactory::getPath('authorization_namespaces'), [
			'headers' => $this->getAdminUser(),
			'output' => $output,
		]);
	}

	public function testUser(): void
	{
		$group = SecurityFactory::getGroup();
		$output = $this->calculateSimpleOutput('UserGroup', null, null, $group);

		$response = $this->testPost(SecurityFactory::getPath('user_groups'), [
			'headers' => $this->getAdminUser(),
			'input' => $group,
			'output' => $output,
		]);

		$data = json_decode($response->getContent(), true);
		$groupUri = $data['@id'];

		$user = SecurityFactory::getUser([ 'groups' => [$groupUri] ]);
		$output = $this->calculateSimpleOutput('User', null, null, $user);
		$response = $this->testPost(SecurityFactory::getPath('users'), [
			'headers' => $this->getAdminUser(),
			'input' => $user,
			'output' => $output,
		]);
		$data = json_decode($response->getContent(), true);
		$userUri = $data['@id'];

		$policy = SecurityFactory::getAutorizationPolicy([ 'groups' => [$groupUri] ]);
		$output = $this->calculateSimpleOutput('AuthorizationPolicy', null, null, $policy);
		$response = $this->testPost(SecurityFactory::getPath('authorization_policies'), [
			'headers' => $this->getAdminUser(),
			'input' => $policy,
			'output' => $output,
		]);
		$data = json_decode($response->getContent(), true);
		$policyUri = $data['@id'];
		$policyNamespace = $data['namespace'];
		$policyRelation = $data['relation'];

		$this->processQueue();
		
		$output = [ 'authorizationsCount'  => 1 ];
		$response = $this->testGet($userUri, [
			'headers' => $this->getAdminUser(),
			'output' => $output,
		]);
		$data = json_decode($response->getContent(), true);
	}

}
