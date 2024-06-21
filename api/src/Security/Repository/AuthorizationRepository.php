<?php

namespace App\Security\Repository;

use App\Security\Entity\Authorization;
use App\Security\Entity\User;

use Beerfranz\RogerBundle\Repository\RogerRepositoryInterface;
use Beerfranz\RogerBundle\Repository\RogerRepositoryTrait;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Authorization>
 *
 * @method Authorization|null find($id, $lockMode = null, $lockVersion = null)
 * @method Authorization|null findOneBy(array $criteria, array $orderBy = null)
 * @method Authorization[]    findAll()
 * @method Authorization[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorizationRepository extends ServiceEntityRepository implements RogerRepositoryInterface
{
	use RogerRepositoryTrait;
	
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Authorization::class);
	}

	public function findUnrefreshedUserAuthorizations(User $user, int $refreshId)
	{
		return $this->createQueryBuilder('a')
					->andWhere('a.user = :user')
					->setParameter('user', $user)
					->andWhere('a.refreshId != :refreshId')
					->setParameter('refreshId', $refreshId)
					->getQuery()
					->getResult();

	}

}
