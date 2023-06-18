<?php

namespace App\Tests\Api;

use App\Tests\Api\Functional;
use App\Tests\Factory\AssetFactory;

use App\Entity\AssetType;

class AssetTest extends Functional
{

  /**
   * Create, read, update, and delete an asset
   **/
  public function testAdminCRUDAsset(): void
  {
    $userHeaders = $this->getAdminUser();

    // Create asset
    $data = [
        'identifier' => 'UnitTest',
      ];
    static::createClient()->request('POST', '/assets', [
      'json' => $data,
      'headers' => $userHeaders
    ]);

    $this->assertResponseStatusCodeSame(201);
    $this->assertJsonContains([
      '@context' => '/contexts/Asset',
      '@type' => 'Asset',
      'identifier' => 'UnitTest',
    ]);

    // Audit creation
    static::createClient()->request('GET', '/asset_audits/1', [ 'headers' => $userHeaders ]);
    $this->assertResponseStatusCodeSame(200);
    $this->assertJsonContains([
      '@context' => '/contexts/AssetAudit',
      '@type' => 'AssetAudit',
      'subject' => 'UnitTest',
      'actor' => $userHeaders['x-token-user-email'],
      'action' => 'create',
      'data' => $data
    ]);

    // Read asset
    static::createClient()->request('GET', '/assets/1',
      [ 'headers' => $userHeaders ]);

    $this->assertResponseStatusCodeSame(200);
    $this->assertJsonContains([
      '@context' => '/contexts/Asset',
      '@type' => 'Asset',
      'identifier' => 'UnitTest',
      'createdBy' => $userHeaders['x-token-user-email'],
    ]);

    // Update asset
    // static::createClient()->request('PATCH', '/assets/1', [ 
    //   'headers' => array_merge($userHeaders, [ 'Content-Type' => 'application/merge-patch+json']),
    //   'json' => [ 'attributes' => [ 'test' => 'one test' ] ] 
    // ]);
    // $this->assertResponseStatusCodeSame(200);
    // $this->assertJsonContains([
    //   '@context' => '/contexts/Asset',
    //   '@type' => 'Asset',
    //   'identifier' => 'UnitTest',
    //   'createdBy' => $userHeaders['x-token-user-email'],
    //   'attributes' => [ 'test' => 'one test' ],
    // ]);

    // Audit update
    // static::createClient()->request('GET', '/asset_audits/2', [ 'headers' => $userHeaders ]);
    // $this->assertResponseStatusCodeSame(200);
    // $this->assertJsonContains([
    //   '@context' => '/contexts/AssetAudit',
    //   '@type' => 'AssetAudit',
    //   'subject' => 'UnitTest',
    //   'action' => 'update',
    //   'actor' => $userHeaders['x-token-user-email'],
    // ]);

    // Delete asset
    static::createClient()->request('DELETE', '/assets/1', [ 'headers' => $userHeaders ]);
    $this->assertResponseStatusCodeSame(204);

    // Audit deletion
    static::createClient()->request('GET', '/asset_audits/2', [ 'headers' => $userHeaders ]);
    $this->assertResponseStatusCodeSame(200);
    $this->assertJsonContains([
      '@context' => '/contexts/AssetAudit',
      '@type' => 'AssetAudit',
      'subject' => 'UnitTest',
      'action' => 'remove',
      'actor' => $userHeaders['x-token-user-email'],
      // 'data' => array_merge($data, [ 'attributes' => [ 'test' => 'one test' ] ]),
    ]);

  }

}
