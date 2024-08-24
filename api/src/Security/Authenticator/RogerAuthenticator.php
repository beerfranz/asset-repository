<?php

namespace App\Security\Authenticator;

use App\Security\Entity\User;
use App\Security\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

use Symfony\Contracts\Cache\TagAwareCacheInterface;


abstract class RogerAuthenticator extends AbstractAuthenticator
{

	public function __construct(
		protected EntityManagerInterface $entityManager,
		protected ParameterBagInterface $parameterBag,
		protected TagAwareCacheInterface $cacheApp,
		protected UserRepository $userRepository
	) {}

	protected ?string $token;
	protected User $user;

	protected function userPassport(string $sub, string $email, array $roles = []): Passport
	{

		$repo = $this->userRepository;
		$user = $repo->findOneBy([ 'subject' => $sub ]);
		if ($user === null)
		{
			if ($this->parameterBag->get('auth.createAuthnUser')) {
				$user = new User();
				$user->setEmail($email);
				$user->setSubject($sub);
				if ($this->parameterBag->get('auth.refreshRoles'))
					$user->setRoles($roles);
				$this->entityManager->persist($user);
				$this->entityManager->flush();
			} else
				throw new AuthenticationException('User not exists');
		} 
		elseif ($this->parameterBag->get('auth.refreshRoles')) {
			$user->setRoles($roles);
			$this->entityManager->persist($user);
			$this->entityManager->flush();
		}

		$this->user = $user;

		return new SelfValidatingPassport(new UserBadge($email, function (string $userIdentifier): ?UserInterface {
                // return $this->userRepository->findOneBy(['email' => $userIdentifier]);
				return $this->user;
            }));

	}
}