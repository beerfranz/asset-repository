<?php

namespace App\Tests\Api;

use App\Tests\Api\Functional;
// use App\Tests\Factory\AssetFactory;

class RiskTest extends Functional
{

  /**
   * Create, read, update, and delete an asset
   **/
  public function testAdminCRUDRiskManager(): void
  {

    // Create asset
    $identifier = 'iso27001';
    $input = [
      'values' => [
        'value' => [
          'description' => 'value description',
          'triggers' => [
            'info' => [
              'trigger' => 'value == 1',
              'description' => 'trigger description',
            ],
            'warning' => [
              'trigger' => 'value == 2',
              'description' => '',
            ],
            'danger' => [
              'trigger' => 'value == 3',
              'description' => '',
            ],
          ],
        ],
        'disponibility' => [],
        'confidentiality' => [],
        'integrity' => [],
      ],
      'valuesAggregator' => 'value * max(disponibility, integrity, confidentiality)',
      "triggers" => [
        'danger' => 'aggregator >= 9',
        'warning' => 'aggregator >= 4',
        'info' => 'aggregator < 4',
      ],
    ];
    $output = [
      '@context' => '/contexts/RiskManager',
      '@id' => '/risk_managers/iso27001',                                                                
      '@type' => 'RiskManager',
    ];

    $this->testIdempotentCrud('/risk_managers/' . $identifier, [
      'headers' => $this->getAdminUser(),
      'input' => $input,
      'output' => $output,
    ]);

  }

}
