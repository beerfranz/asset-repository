<?php

namespace App\Tests\Api;

use App\Tests\Api\Functional;

use App\Setting\Service\SettingService;

class SettingTest extends Functional
{

  /**
   * Get settings collection
   **/
  public function testGetSettings(): void
  {

    $container = static::getContainer();
    $settingService = $container->get(SettingService::class);
    $settingService->initSettings();

    $output = [
      'hydra:totalItems' => 4,
    ];

    $this->testGetCollection('/settings', [
      'headers' => $this->getAdminUser(),
      'output' => $output,
    ]);

  }

}
