<?php

namespace App\Service;

use App\Entity\Asset;
use App\Entity\Instance;

use App\Service\UserTemplate;

use Doctrine\ORM\EntityManagerInterface;

use Psr\Log\LoggerInterface;

class InstanceReconciliation
{
  protected $logger;
  protected $entityManager;
  protected $assetRepo;
  protected $instanceRepo;
  protected $userTemplateService;

  public function __construct(
    EntityManagerInterface $entityManager,
    LoggerInterface $logger,
    UserTemplate $userTemplateService,
  ) {
    $this->entityManager = $entityManager;
    $this->logger = $logger;
    $this->userTemplateService = $userTemplateService;

    $this->assetRepo = $entityManager->getRepository(Asset::class);
    $this->instanceRepo = $entityManager->getRepository(Instance::class);
  }


  public function reconcile() {
    foreach ($this->getInstances() as $instance) {
      $this->reconcileInstance($instance);
    }
  }

  public function reconcileInstance(Instance $instance): Instance
  {
    $candidateAssets = [];
    foreach($this->getAssetRules() as $asset) {
      $notThisAsset = 0;
      foreach($asset['rules'] as $ruleName => $rule) {
        $userTemplate = $this->userTemplateService->test($rule, $this->getInstanceAttributes($instance));
        // $sanitizedRule = $this->sanitizeRule($rule);

        if (! $userTemplate->getBoolResult()) {
        // try {
        //   if (! eval("return $sanitizedRule;"))
            $notThisAsset++;
        } 
        // catch(\Error $e) {
        //   $notThisAsset++;
        //   $this->logger->error('Failed to interpret reconcilation rule ' . $ruleName . ' of asset ' . $asset['id'] . '. Sanitized rule: ' . $sanitizedRule);
        //   continue;
        // }
      }

      if ($notThisAsset === 0)
        $candidateAssets[] = $asset['id'];
    }

    if (count($candidateAssets) === 1) {
      $asset = $this->assetRepo->find($candidateAssets[0]);
      $instance->setAsset($asset);
    } elseif (count($candidateAssets) === 0) {
      $instance->setAsset(null);
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

  protected function getInstanceAttributes($instance): Array
  {
    return [
      'friendlyName' => $instance->getFriendlyName(),
      'kind' => $instance->getKindIdentifier(),
    ];
  }

  protected function getInstanceQueryLanguageMapping(): Array
  {
    return [
      'friendlyName' => '$instance->getFriendlyName()',
      'kind' => '$instance->getKindIdentifier()',
    ];
  }
}
