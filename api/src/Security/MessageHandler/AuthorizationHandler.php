<?php

namespace App\Security\MessageHandler;

use App\Security\Entity\User;
use App\Security\Entity\UserGroup;
use App\Security\Entity\AuthorizationPolicy;
use App\Security\Service\AuthorizationService;

use Beerfranz\RogerBundle\Message\RogerAsyncMessage;
use Beerfranz\RogerBundle\MessageHandler\RogerHandlerAbstract;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Psr\Log\LoggerInterface;


class AuthorizationHandler extends RogerHandlerAbstract
{

	protected $userRepo;
	protected $userGroupRepo;
	protected $policyRepo;

	public function __construct(
		protected AuthorizationService $service,
		protected LoggerInterface $logger,
		protected EntityManagerInterface $entityManager,
	)
	{
		$this->userRepo = $entityManager->getRepository(User::class);
		$this->userGroupRepo = $entityManager->getRepository(UserGroup::class);
		$this->policyRepo = $entityManager->getRepository(AuthorizationPolicy::class);
	}

	#[AsMessageHandler]
	public function rogerMessage(RogerAsyncMessage $message)
	{
		$this->handlerName = __METHOD__;
		$this->messageClass = $message::class;

		$event = $message->getEvent();
		$context = $message->getContext();

		if ($this->isAboutEntityNames($context, [ 'User' ])) {
			$userId = $context['entity']['id'];
			$user = $this->userRepo->find($userId);
			$this->service->refreshUserAuthorizations($user);
		}

		if ($this->isAboutEntityNames($context, [ 'UserGroup' ])) {
			$groupId = $context['entity']['id'];
			$group = $this->userGroupRepo->find($groupId);

			foreach ($group->getUsers() as $user) {
				$this->service->refreshUserAuthorizations($user);
			}
		}

		if ($this->isAboutEntityNames($context, [ 'AuthorizationPolicy' ])) {
			$policyId = $context['entity']['id'];
			$policy = $this->policyRepo->find($policyId);

			foreach ($policy->getGroups() as $group) {
				foreach ($group->getUsers() as $user) {
					$this->service->refreshUserAuthorizations($user);
				}
			}
		}
	}

}