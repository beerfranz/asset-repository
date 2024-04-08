<?php

namespace App\Tests\Api;

use App\Tests\Api\Functional;
// use App\Tests\Factory\AssetFactory;

class TaskTest extends Functional
{

  /**
   * Create, read, update, and delete an asset
   **/
  public function testAdminCRUDTask(): void
  {
    $userHeaders = $this->getAdminUser();

    // Create asset
    $identifier = 'UnitTest';
    $input = [
      'title' => 'test title',
      'description' => 'test description',
    ];

    $output = array_merge($input, [
      '@context' => '/contexts/Task',
      '@id' => '/tasks/' . $identifier,
      '@type' => 'Task',
      'identifier' => $identifier,
    ]);

    static::createClient()->request('GET', '/tasks/' . $identifier,
      [ 'headers' => $userHeaders ]);
    $this->assertResponseStatusCodeSame(404);

    static::createClient()->request('PUT', '/tasks/' . $identifier , [
      'json' => $input,
      'headers' => $userHeaders
    ]);

    $this->assertResponseStatusCodeSame(200);
    $this->assertJsonContains($output);

    // Read task
    static::createClient()->request('GET', '/tasks/' . $identifier,
      [ 'headers' => $userHeaders ]);

    $this->assertResponseStatusCodeSame(200);
    $this->assertJsonContains($output);

    // Delete task
    static::createClient()->request('DELETE', '/tasks/' . $identifier,
      [ 'headers' => $userHeaders ]);
    $this->assertResponseStatusCodeSame(204);

    static::createClient()->request('GET', '/tasks/' . $identifier,
      [ 'headers' => $userHeaders ]);
    $this->assertResponseStatusCodeSame(404);

  }

}
