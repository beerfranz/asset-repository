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

	public function __construct(
		protected AuthorizationService $service,
		protected LoggerInterface $logger,
		protected EntityManagerInterface $entityManager,
	)
	{

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
			$userRepo = $this->entityManager->getRepository(User::class);

			$user = $userRepo->find($userId);
			$this->service->refreshUserAuthorizations($user);
		}

		// if ($this->isAboutEntityNames($context, [ 'UserGroup' ])) {
		// 	$groupId = $context['entity']['id'];
		// 	$userGroupRepo = $this->entityManager->getRepository(UserGroup::class);

		// 	$group = $userGroupRepo->find($groupId);

		// 	foreach ($group->getUsers() as $user) {
		// 		$this->service->refreshUserAuthorizations($user);
		// 	}
		// }
	
	}

}