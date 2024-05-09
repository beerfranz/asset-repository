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

    $output = $this->calculateSimpleOutput('Task', $identifier, '/tasks/' . $identifier, $input);

    $this->testIdempotentCrud('/tasks/' . $identifier, [
      'headers' => $this->getAdminUser(),
      'input' => $input,
      'output' => $output,
      'withAudit' => true,
    ]);

  }

}
