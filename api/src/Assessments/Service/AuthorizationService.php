<?php

namespace App\Assessments\Service;

use App\Security\Entity\Authorization;
use App\Security\Service\AuthorizationService as AuthorizationServiceBase;

use Beerfranz\RogerBundle\Service\RogerService;

use Doctrine\ORM\EntityManagerInterface;

use Psr\Log\LoggerInterface;

class AuthorizationService extends AuthorizationServiceBase
{

	public function isAssessmentsEnabled($user)
	{
		return $this->isNamespaceEnabled($user, 'assessments');
	}

}
