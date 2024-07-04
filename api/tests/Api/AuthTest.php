<?php

namespace App\Tests\Api;

use App\Tests\Api\Functional;

class AuthTest extends Functional
{

  public function testAuthWithoutEmailFails(): void
  {
    static::createClient()->request('GET', '/assets');
    $this->assertResponseStatusCodeSame(401);
  }

  public function testAuthWithoutRoleFails(): void
  {
    static::createClient()->request('GET', '/assets', [ 'headers' => [ 'x-token-user-email' => 'unittest-admin@local.com' ] ]);
    $this->assertResponseStatusCodeSame(401);
  }

  public function testAuth(): void
  {
    static::createClient()->request('GET', '/ui', [
      'headers' => [
        'x-token-user-email' => 'unittest-admin@local.com',
        'x-token-subject' => '666',
        'x-token-user-roles' => 'ROLE_ADMIN ROLE_USER',
      ] ]);
    $this->assertResponseStatusCodeSame(200);
  }
}
