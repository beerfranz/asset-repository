<?php

namespace App\Tests\Api;

use App\Tests\Api\Functional;
use App\Tests\Factory\AssetFactory;
use App\Tests\Factory\AssetTypeFactory;

use App\Entity\AssetType;

class AssetBatchTest extends Functional
{

  public function testAdminBatchAssetPost(): void
  {
    $client = $this->getClientAdmin();

    $assetType = AssetTypeFactory::createOne();
    $assetTypeIri = $this->findIriBy(AssetType::class, ['name' => $assetType->getName() ]);

    $client->request('POST', '/batch_assets', [
      'json' => [
        'assets' => [
          [
            'identifier' => 'UnitTest1',
            'type' => $assetTypeIri,
          ],
          [
            'identifier' => 'UnitTest2',
            'type' => $assetTypeIri,
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

}
