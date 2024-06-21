<?php

namespace App\Security\Service;

use App\Security\Entity\Authorization;
use App\Security\Entity\AuthorizationPolicy;
use App\Security\Entity\User;

use Beerfranz\RogerBundle\Service\RogerService;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Scheduler\Trigger\CronExpressionTrigger;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

use Psr\Log\LoggerInterface;

class AuthorizationService extends RogerService
{
	protected $frequencyService;
	protected $taskRepo;
	protected $taskTemplateRepo;
	protected $userTemplateService;

	public function __construct(
		EntityManagerInterface $entityManager,
		LoggerInterface $logger,
	) {
		parent::__construct($entityManager, $logger, Authorization::class);

		
	}

	public function newEntity(): Authorization
	{
		$authorization = new Authorization();

		return $authorization;
	}

	public function refreshUserAuthorizations(User $user)
	{
		$refreshId = time();
		foreach ($user->getGroups() as $group) {
			foreach ($group->getAuthorizationPolicies() as $authorizationPolicy) {
				$this->refreshUserAuthorization($user, $authorizationPolicy, $refreshId);
			}
		}

		foreach ($this->repo->findUnrefreshedUserAuthorizations($user, $refreshId) as $authorization) {
			$this->deleteEntity($authorization);
		}
	}

	protected function refreshUserAuthorization(User $user, AuthorizationPolicy $authorizationPolicy, int $refeshId)
	{
		$authorization = $this->repo->findOneBy([
			'user' => $user,
			'namespace' => $authorizationPolicy->getNamespace(),
			'object' => $authorizationPolicy->getObject(),
			'relation' => $authorizationPolicy->getRelation(),
			'context' => $authorizationPolicy->getContext(),
		]);

		if ($authorization === null) {
			$authorization = new Authorization([
				'user' => $user,
				'namespace' => $authorizationPolicy->getNamespace(),
				'object' => $authorizationPolicy->getObject(),
				'relation' => $authorizationPolicy->getRelation(),
				'context' => $authorizationPolicy->getContext(),
			]);
		}

		$authorization->setRefreshId($refeshId);
		$this->persistEntity($authorization);
	}


}