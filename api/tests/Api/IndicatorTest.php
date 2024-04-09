<?php

namespace App\Tests\Api;

use App\Tests\Api\Functional;
// use App\Tests\Factory\AssetFactory;

class IndicatorTest extends Functional
{

  /**
   * Create, read, update, and delete an asset
   **/
  public function testAdminCRUDIndicator(): void
  {

    // Create asset
    $identifier = 'UnitTest';
    $input = [
      'description' => 'test description',
      'namespace' => 'test namespace',
      'targetValue' => 80,
      'triggers' => [
        'danger' => 'value < 60',
        'warning' => 'value < 70',
      ],
      'frequency' => [
        'description' => 'monthly',
        'crontab' => '0 0 1 * * ',
      ],
    ];

    $output = $this->calculateSimpleOutput('Indicator', $identifier, '/indicators/' . $identifier, $input);

    $this->testIdempotentCrud('/indicators/' . $identifier, [
      'headers' => $this->getAdminUser(),
      'input' => $input,
      'output' => $output,
    ]);

  }

}
