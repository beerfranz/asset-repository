<?php

namespace App\Service;

use App\Entity\Asset;
use App\Entity\Risk;

use App\Service\UserTemplate;

use Doctrine\ORM\EntityManagerInterface;

use Psr\Log\LoggerInterface;

class RiskService
{
  protected $logger;
  protected $entityManager;
  protected $userTemplate;
  protected $assetRepo;
  protected $riskRepo;

  public function __construct(
    EntityManagerInterface $entityManager,
    LoggerInterface $logger,
    UserTemplate $userTemplate,
  ) {
    $this->entityManager = $entityManager;
    $this->logger = $logger;
    $this->userTemplate = $userTemplate;

    $this->assetRepo = $entityManager->getRepository(Asset::class);
    $this->riskRepo = $entityManager->getRepository(Risk::class);
  }

  public function getAggregatedRisk($riskManager, $risks)
  {
    $values = $riskManager->getValues();
    $aggregator = $riskManager->getValuesAggregator();
    
    $risk = $risks[1];

    $riskValues = $risk->getValues();

    $initialRisk = $this->userTemplate->template('{{ ' . $aggregator . '}}', $riskValues);
    $initialTrigger = $this->getTrigger($riskManager, $initialRisk);

    $mitigatedRiskValues = $riskValues;

    foreach($risk->getMitigations() as $mitigation) {
      foreach($mitigation['effects'] as $value => $effect) {
        $mitigatedRiskValues[$value] = $effect;
      }
    }

    $mitigatedRisk = $this->userTemplate->template('{{ ' . $aggregator . '}}', $mitigatedRiskValues);
    $mitigatedTrigger = $this->getTrigger($riskManager, $mitigatedRisk);

    return [ 
      'initialRisk' => [ 'value' => $initialRisk, 'trigger' => $initialTrigger ],
      'mitigatedRisk' => [ 'value' => $mitigatedRisk, 'trigger' => $mitigatedTrigger ]
    ];
  }

  protected function getTrigger($riskManager, $riskValue)
  {
    $triggersToTest = [ 'danger', 'warning', 'info' ];

    $triggers = $riskManager->getTriggers();

    $trigger = '';

    foreach($triggersToTest as $triggerToTest) {
      if (isset($triggers[$triggerToTest])) {
        $test = $this->userTemplate->template('{{ ' . $triggers[$triggerToTest] . ' ? "true" : "false" }}', [ 'aggregator' => $riskValue ]);
        if ($test === 'true') {
          $trigger = $triggerToTest;
          break;
        }
      }
    }

    return $trigger;
  }
}