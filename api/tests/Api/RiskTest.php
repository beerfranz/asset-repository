<?php

namespace App\Tests\Api;

use App\Tests\Api\Functional;
// use App\Tests\Factory\AssetFactory;

class RiskTest extends Functional
{

  public static function getRiskManagerInput()
  {
    return [
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
  }

  public static function getRiskInput()
  {
    return [
      'asset' => [
        'identifier' => 'assetUnitTest',
      ],
      'riskManager' => [
        'identifier' => 'unitTest'
      ],
      'description' => 'test description',
      'values' => [
        'disponibility' => 3,
        'integrity' => 3,
        'confidentiality' => 1,
        'value' => 3,
      ],
      'mitigations' => [
        'mit1' => [
          'description' => 'test mitigation 1',
          'effects' => [
            'disponibility' => 1,
          ]
        ],
        'mit2' => [
          'description' => 'test mitigation 2',
          'effects' => [
            'integrity' => 1,
          ],
        ]
      ],
    ];
  }

  public static function getAssetInput()
  {
    return [
      'identifier' => 'assetUnitTest',
      'attributes' => [
        'price' => 1000
      ],
    ];
  }

  /**
   * Create, read, update, and delete an asset
   **/
  public function testAdminCRUDRiskManager(): void
  {

    // Create riskManager
    $identifier = 'unitTest';
    $input = self::getRiskManagerInput();
    
    $output = $this->calculateSimpleOutput('RiskManager', $identifier, '/risk_managers/' . $identifier, $input);

    $this->testIdempotentCrud('/risk_managers/' . $identifier, [
      'headers' => $this->getAdminUser(),
      'input' => $input,
      'output' => $output,
    ]);

  }

  public function testAdminCRUDRisk(): void
  {

    // Create riskManager
    $riskManagerIdentifier = 'unitTest';
    $riskManagerInput = self::getRiskManagerInput();
    
    $riskManagerOutput = $this->calculateSimpleOutput('RiskManager', $riskManagerIdentifier, '/risk_managers/' . $riskManagerIdentifier, $riskManagerInput);

    $this->testPut('/risk_managers/' . $riskManagerIdentifier, [
      'headers' => $this->getAdminUser(),
      'input' => $riskManagerInput,
      'output' => $riskManagerOutput,
    ]);

    // Create asset
    $assetIdentifier = 'assetUnitTest';
    $assetInput = self::getAssetInput();
    $assetOutput = $this->calculateSimpleOutput('Asset', $assetIdentifier, '/assets/' . $assetIdentifier, $assetInput);
    $assetOutput['@id'] = '/assets/1';
    $this->testPost('/assets', [
      'headers' => $this->getAdminUser(),
      'input' => $assetInput,
      'output' => $assetOutput,
    ]);

    // Create risk with mitigations
    $riskIdentifier = 'unitTest';
    $riskInput = self::getRiskInput();
    $riskOutput = $this->calculateSimpleOutput('Risk', $riskIdentifier, '/risks/' . $riskIdentifier, $riskInput);
    $riskOutput['mitigations']['mit1']['aggregatedRisk'] = ['value' => '9', 'trigger' => 'danger'];
    $riskOutput['mitigations']['mit2']['aggregatedRisk'] = ['value' => '3', 'trigger' => 'info'];
    $riskOutput['asset'] = $riskOutput['asset']['identifier'];
    $riskOutput['riskManager'] = $riskOutput['riskManager']['identifier'];

    $this->testPut('/risks/' . $riskIdentifier, [
      'headers' => $this->getAdminUser(),
      'input' => $riskInput,
      'output' => $riskOutput,
    ]);

  }


}
