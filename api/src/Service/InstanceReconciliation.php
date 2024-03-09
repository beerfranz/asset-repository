<?php

namespace App\Service;

use App\Entity\Asset;
use App\Entity\Instance;

use Doctrine\ORM\EntityManagerInterface;

use Psr\Log\LoggerInterface;

class InstanceReconciliation
{
  protected $logger;
  protected $entityManager;
  protected $assetRepo;
  protected $instanceRepo;

  public function __construct(
    EntityManagerInterface $entityManager,
    LoggerInterface $logger,
  ) {
    $this->entityManager = $entityManager;
    $this->logger = $logger;

    $this->assetRepo = $entityManager->getRepository(Asset::class);
    $this->instanceRepo = $entityManager->getRepository(Instance::class);
  }


  public function reconcile() {
    foreach ($this->getInstances() as $instance) {
      $this->reconcileInstance($instance);
    }
  }

  public function reconcileInstance($instance): Instance
  {
    $candidateAssets = [];
    foreach($this->getAssetRules() as $asset) {
      $notThisAsset = 0;
      foreach($asset['rules'] as $ruleName => $rule) {
        $sanitizedRule = $this->sanitizeRule($rule);
        try {
          if (! eval("return $sanitizedRule;"))
            $notThisAsset++;
        } catch(\Error $e) {
          $notThisAsset++;
          $this->logger->error('Failed to interpret reconcilation rule ' . $ruleName . ' of asset ' . $asset['id'] . '. Sanitized rule: ' . $sanitizedRule);
          continue;
        }
      }

      if ($notThisAsset === 0)
        $candidateAssets[] = $asset['id'];
    }

    if (count($candidateAssets) === 1) {
      $asset = $this->assetRepo->find($candidateAssets[0]);
      $instance->setAsset($asset);
      $this->entityManager->persist($instance);
      $this->entityManager->flush();
      $this->entityManager->clear();
    } elseif (count($candidateAssets) === 0) {
      $instance->setAsset(null);
      $this->entityManager->persist($instance);
      $this->entityManager->flush();
      $this->entityManager->clear();
    }

    return $instance;
  }

  protected function getAssetRules() {
    return $this->assetRepo->findRules();
  }

  protected function getInstances() {
    return $this->instanceRepo->findAll();
  }

  protected function sanitizeRule($rule): string
  {
    $instanceMapping = $this->getInstanceQueryLanguageMapping();

    $sanitizedRule = str_replace(array_keys($instanceMapping), array_values($instanceMapping), $rule);
    return $sanitizedRule;
  }

  protected function getInstanceQueryLanguageMapping(): Array
  {
    return [
      'friendlyName' => '$instance->getFriendlyName()',
      'kind' => '$instance->getKindIdentifier()',
    ];
  }
}
