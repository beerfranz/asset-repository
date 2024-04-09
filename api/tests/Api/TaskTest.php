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

    $this->testIdempotentCrud('/tasks/' . $identifier, [
      'headers' => $this->getAdminUser(),
      'input' => $input,
      'output' => $output,
    ]);

  }

}
