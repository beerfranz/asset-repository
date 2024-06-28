<?php

namespace App\Common\Service;

use App\Common\Entity\Trigger;
use App\Common\Service\UserTemplate;

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

  public function calculateTrigger(array $rules, $valueToTest): Trigger
  {
  	$trigger = new Trigger([ 'rules' => $rules, 'printLevel' => 'success' ]);

  	foreach($rules as $level => $rule) {
  		$check = $this->userTemplateService->test($rule, [ 'value' => $valueToTest ]);

  		if ($check->getBoolResult()) {
  			$trigger->setPrintLevel($level);
  			break;
  		}
  	}

  	return $trigger;
  }

}