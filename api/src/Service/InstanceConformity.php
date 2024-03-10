<?php

namespace App\Service;

use App\Entity\Asset;
use App\Entity\Instance;

use Doctrine\ORM\EntityManagerInterface;

use Psr\Log\LoggerInterface;

class InstanceConformity
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

  public function checkInstance(Instance $instance): Instance
  {

    $asset = $instance->getAsset();

    if ($asset === null) {
      $instance->setIsConform(null);
      $instance->setConformities(null);
      return $instance;
    }

    $countTotal = 0;
    $countError = 0;

    $conformities = [ 'errors' => [], 'validated' => [], 'date' => date('Y-m-d H:i:s'), 'statistics' => []];

    $instanceAttributes = $instance->getAttributes();

    foreach ($asset->getAttributes() as $category => $attributes) {
      foreach ($attributes as $attribute => $constraint) {
        $countTotal++;
        if (isset($instanceAttributes[$category][$attribute])) {
          $attributeValue = $instanceAttributes[$category][$attribute];

          $check = $this->checkWithQueryLanguage($constraint, $attributeValue);

          if ($check) {
            $conformities['validated']['attributes'][$category][$attribute] = [
              'constraint' => $constraint,
              'value' => $attributeValue,
              'isConform' => true,
            ];
          } else {
            $conformities['errors']['attributes'][$category][$attribute] = [
              'constraint' => $constraint,
              'value' => $attributeValue,
              'isConform' => false,
              'reason' => 'Not in constraint: ' . $constraint,
            ];
            $countError++;
          }

        } else {
          $conformities['errors']['attributes'][$category][$attribute] = [
            'constraint' => $constraint,
            'value' => null,
            'isConform' => false,
            'reason' => 'Not defined',
          ];
          $countError++;
        }
      }
    }

    $conformities['statistics']['errors'] = $countError;
    $conformities['statistics']['total'] = $countTotal;

    $instance->setConformities($conformities);

    if ($countError > 0)
      $instance->setIsConform(false);
    elseif ($countTotal > 0)
      $instance->setIsConform(true);
    else
      $instance->setIsConform(null);

    return $instance;
  }

  protected function checkWithQueryLanguage ($constraint, $attributeValue): bool
  {
    preg_match('/^([^ ]*)(.*)$/', $constraint, $matches);

    if ($matches[1] == 'in' && isset($matches[2]))
      $check = in_array($attributeValue, json_decode($matches[2]));
    else
      $check = $attributeValue === $matches[0];

    return $check;
  }
}
