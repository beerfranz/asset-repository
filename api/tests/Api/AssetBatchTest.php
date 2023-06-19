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

    $client->request('POST', '/sources/unit_test_batch_asset/assets', [
      'json' => [
        'owner' => [ 'identifier' => 'Someone' ],
        'assets' => [
          [
            'identifier' => 'UnitTest1',
            'kind' => [ 'identifier' => 'app' ],
          ],
          [
            'identifier' => 'UnitTest2',
            'kind' => [ 'identifier' => 'app' ],
            'owner' => [ 'identifier' => 'CISO' ],
          ],
        ]
      ]
    ]);

    $this->assertResponseStatusCodeSame(201);
    $this->assertJsonContains([
      '@context' => [ 'assets' => 'AssetBatchDto/assets' ],
      '@type' => 'AssetBatchDto',
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
    $client->request('PUT', '/sources/unit_test_batch_asset/assets', [
      'json' => [
        'owner' => [ 'identifier' => 'Someone' ],
        'assets' => [
          [
            'identifier' => 'UnitTest1',
            'kind' => [ 'identifier' => 'app' ],
          ],
          [
            'identifier' => 'UnitTest2',
            'kind' => [ 'identifier' => 'app' ],
          ],
        ]
      ]
    ]);
    $this->assertResponseStatusCodeSame(200);

    // Update 1 asset, create a third asset, implicitly delete 1 asset
    $client->request('PUT', '/sources/unit_test_batch_asset/assets', [
      'json' => [
        'owner' => [ 'identifier' => 'Someone' ],
        'assets' => [
          [
            'identifier' => 'UnitTest1',
            'kind' => [ 'identifier' => 'app' ],
            'attributes' => [ 'test' => 'an attribute' ]
          ],
          [
            'identifier' => 'UnitTest3',
            'kind' => [ 'identifier' => 'app' ],
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
