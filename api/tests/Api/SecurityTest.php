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

		$this->testCrud('/users', [
			'headers' => $this->getAdminUser(),
			'input' => $user,
			'output' => $output,
		]);
	}

	public function testCrudGroup(): void
	{
		$group = SecurityFactory::getGroup();
		
		$output = $this->calculateSimpleOutput('UserGroup', null, null, $group);

		$this->testCrud('/user_groups', [
			'headers' => $this->getAdminUser(),
			'input' => $group,
			'output' => $output,
		]);

	}

	public function testUser(): void
	{
		$group = SecurityFactory::getGroup();
		$output = $this->calculateSimpleOutput('UserGroup', null, null, $group);

		$response = $this->testPost('/user_groups', [
			'headers' => $this->getAdminUser(),
			'input' => $group,
			'output' => $output,
		]);

		$data = json_decode($response->getContent(), true);
		$groupUri = $data['@id'];

		$user = SecurityFactory::getUser([ 'groups' => [$groupUri] ]);
		$output = $this->calculateSimpleOutput('User', null, null, $user);
		$this->testPost('/users', [
			'headers' => $this->getAdminUser(),
			'input' => $user,
			'output' => $output,
		]);


	}

}
