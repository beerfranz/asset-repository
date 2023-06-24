<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
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

    protected function userPassport(string $email, array $roles = []): Passport
    {

        $repo = $this->userRepository;
        $user = $repo->findOneBy([ 'email' => $email ]);
        if ($user === null)
        {
            $user = new User();
            $user->setEmail($email);
            $user->setRoles($roles);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } else {
            $user->setRoles($roles);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        return new SelfValidatingPassport(new UserBadge($email));

    }
}