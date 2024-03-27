<?php

namespace App\Service;

use App\Service\UserTemplate;

use Doctrine\ORM\EntityManagerInterface;

use Psr\Log\LoggerInterface;

class RiskService
{
  protected $logger;
  protected $entityManager;
  protected $userTemplate;

  public function __construct(
    EntityManagerInterface $entityManager,
    LoggerInterface $logger,
    UserTemplate $userTemplate,
  ) {
    $this->entityManager = $entityManager;
    $this->logger = $logger;
    $this->userTemplate = $userTemplate;
  }

  public function aggregateRisk(string $formula, array $values, array $triggers = []): array
  {
    $userTemplate = $this->userTemplate->template('{{ ' . $formula . '}}', $values);
    $aggregatedRisk = $userTemplate->getResult();
    $trigger = $this->getTrigger($triggers, $aggregatedRisk);

    return [ 'value' => $aggregatedRisk, 'trigger' => $trigger ];
  }

  protected function getTrigger($triggers, $riskValue)
  {
    $triggersToTest = [ 'danger', 'warning', 'info' ];

    $trigger = '';

    foreach($triggersToTest as $triggerToTest) {
      if (isset($triggers[$triggerToTest])) {
        $userTemplate = $this->userTemplate->test($triggers[$triggerToTest], [ 'aggregator' => $riskValue ]);
        if ($userTemplate->getBoolResult()) {
          $trigger = $triggerToTest;
          break;
        }
      }
    }

    return $trigger;
  }
}