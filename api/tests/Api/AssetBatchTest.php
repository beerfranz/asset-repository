<?php

namespace App\Tests\Api;

use App\Tests\Api\Functional;
use App\Tests\Factory\AssetFactory;

use App\Entity\AssetType;

class AssetBatchTest extends Functional
{

  public function testAdminBatchAssetPost(): void
  {
    $client = $this->getClientAdmin();

    $client->request('POST', '/batch_assets', [
      'json' => [
        'assets' => [
          [
            'identifier' => 'UnitTest1',
          ],
          [
            'identifier' => 'UnitTest2',
          ],
        ]
      ]
    ]);

    $this->assertResponseStatusCodeSame(201);
    $this->assertJsonContains([
      '@context' => '/contexts/BatchAsset',
      '@type' => 'BatchAsset',
      'assets' => [
        [ 'identifier' => 'UnitTest1' ],
        [ 'identifier' => 'UnitTest2' ],
      ]
    ]);

    // Audit creation
    $client->request('GET', '/asset_audits/1');
    $this->assertResponseStatusCodeSame(200);
    $this->assertJsonContains([
      '@context' => '/contexts/AssetAudit',
      '@type' => 'AssetAudit',
      'subject' => 'UnitTest1',
      'action' => 'create',
    ]);

    $client->request('GET', '/asset_audits/2');
    $this->assertResponseStatusCodeSame(200);
    $this->assertJsonContains([
      '@context' => '/contexts/AssetAudit',
      '@type' => 'AssetAudit',
      'subject' => 'UnitTest2',
      'action' => 'create',
    ]);
  }

  public function testAdminBatchAssetPut(): void
  {
    $client = $this->getClientAdmin();

    // Create 2 assets
    $client->request('PUT', '/batch_assets', [
      'json' => [
        'assets' => [
          [
            'identifier' => 'UnitTest1',
          ],
          [
            'identifier' => 'UnitTest2',
          ],
        ]
      ]
    ]);
    $this->assertResponseStatusCodeSame(201);

    // Update 1 asset, create a third asset, implicitly delete 1 asset
    $client->request('PUT', '/batch_assets', [
      'json' => [
        'assets' => [
          [
            'identifier' => 'UnitTest1',
            'attributes' => [ 'test' => 'an attribute' ]
          ],
          [
            'identifier' => 'UnitTest3',
          ],
        ]
      ]
    ]);

    $client->request('GET', '/assets');
    $this->assertResponseStatusCodeSame(200);
    $this->assertJsonContains([
      '@context' => '/contexts/Asset',
      '@type' => 'hydra:Collection',
      'hydra:totalItems' => 2,
      'hydra:member' => [
        [ 'identifier' => 'UnitTest1' ],
        [ 'identifier' => 'UnitTest3' ],
      ]
    ]);

    $client->request('GET', '/asset_audits');
    $this->assertResponseStatusCodeSame(200);
    $this->assertJsonContains([
      '@context' => '/contexts/AssetAudit',
      '@type' => 'hydra:Collection',
      'hydra:totalItems' => 5,
      'hydra:member' => [
        [ 'subject' => 'UnitTest1', 'action' => 'create' ],
        [ 'subject' => 'UnitTest2', 'action' => 'create' ],
        [ 'subject' => 'UnitTest1', 'action' => 'update' ],
        [ 'subject' => 'UnitTest3', 'action' => 'create' ],
        [ 'subject' => 'UnitTest2', 'action' => 'remove' ],
      ]
    ]);
  }
}
