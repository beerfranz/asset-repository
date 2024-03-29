<?php

namespace App\Service;

use App\Service\UserTemplate;

use Doctrine\ORM\EntityManagerInterface;

use Psr\Log\LoggerInterface;

class TriggerService
{

  protected $logger;
  protected $entityManager;
  protected $userTemplateService;

  public function __construct(
    EntityManagerInterface $entityManager,
    LoggerInterface $logger,
    UserTemplate $userTemplateService,
  ) {
    $this->entityManager = $entityManager;
    $this->logger = $logger;
    $this->userTemplateService = $userTemplateService;
  }

  public function calculateTrigger($triggers, $valueToTest)
  {
  	$trigger = [];
  	$trigger['rules'] = $triggers;
  	$trigger['printLevel'] = 'success';

  	foreach($triggers as $level => $rule) {
  		$check = $this->userTemplateService->test($rule, [ 'value' => $valueToTest ]);

  		if ($check->getBoolResult()) {
  			$trigger['printLevel'] = $level;
  			break;
  		}
  	}

  	return $trigger;
  }

}