<?php

namespace App\Security\Repository;

use App\Security\Entity\AuthorizationPolicy;

use Beerfranz\RogerBundle\Repository\RogerRepositoryInterface;
use Beerfranz\RogerBundle\Repository\RogerRepositoryTrait;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AuthorizationPolicy>
 *
 * @method AuthorizationPolicy|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuthorizationPolicy|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuthorizationPolicy[]    findAll()
 * @method AuthorizationPolicy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorizationPolicyRepository extends ServiceEntityRepository implements RogerRepositoryInterface
{
	use RogerRepositoryTrait;
	
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, AuthorizationPolicy::class);
	}

}
