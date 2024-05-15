<?php

namespace App\Service;

use App\Entity\Setting;

use Beerfranz\RogerBundle\Service\RogerService;

use Doctrine\ORM\EntityManagerInterface;

use Psr\Log\LoggerInterface;

class SettingService extends RogerService
{

  public function __construct(
    EntityManagerInterface $entityManager,
    LoggerInterface $logger,
  ) {
    parent::__construct($entityManager, $logger, Setting::class);
  }

  public function newEntity(): Setting
  {
    $entity = new Setting();

    return $entity;
  }

  public function getDefaultValues()
  {
    return [
      'module_architecture' => [ 'enabled' => true ],
      'module_versions' => [ 'enabled' => true ],
      'module_riskManager' => [ 'enabled' => true ],
      'module_indicators' => [ 'enabled' => true ],
    ];
  }

  public function initSettings() {
    foreach($this->getDefaultValues() as $identifier => $value) {
      if (null === $this->repo->findOneByIdentifier($identifier)) {
        $setting = new Setting(['identifier' => $identifier, 'value' => $value]);
        $this->persistEntity($setting);
      }
    }
  }

  public function isModuleActivated($module): bool
  {
    $identifier = 'module_' . $module;
    $moduleSettings = $this->repo->findOneByIdentifier($identifier);
    if (null === $moduleSettings)
      return false;

    return $moduleSettings->getValue()['enabled'];
  }

}
