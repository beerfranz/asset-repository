<?php

namespace App\Tests\Api;

use App\Tests\Api\Functional;
use App\Tests\Factory\AssetTypeFactory;

class AssetTypeTest extends Functional
{

  public function testCreateType(): void
  {
    static::createClient()->request('POST', '/asset_types', [
      'json' => [
        'name' => 'UnitTest',
      ],
      'headers' => $this->getAdminUser()
    ]);

    $this->assertResponseStatusCodeSame(201);
    $this->assertJsonContains([
      '@context' => '/contexts/AssetType',
      '@type' => 'AssetType',
      'name' => 'UnitTest',
    ]);
  }

  public function testGetType(): void
  {
    $assetType = AssetTypeFactory::createOne();

    static::createClient()->request('GET', '/asset_types/' . $assetType->getName(),
      [ 'headers' => $this->getAdminUser() ]);

    $this->assertResponseStatusCodeSame(200);
    $this->assertJsonContains([
      '@context' => '/contexts/AssetType',
      '@type' => 'AssetType',
      'name' => $assetType->getName(),
    ]);
  }

  public function testDeleteType(): void
  {
    $assetType = AssetTypeFactory::createOne();

    static::createClient()->request('DELETE', '/asset_types/' . $assetType->getName(),
      [ 'headers' => $this->getAdminUser() ]);

    $this->assertResponseStatusCodeSame(204);
  }
}
