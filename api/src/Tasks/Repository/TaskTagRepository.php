<?php

namespace App\Tasks\Repository;

use App\Tasks\Entity\TaskTag;

use Beerfranz\RogerBundle\Repository\RogerRepositoryInterface;
use Beerfranz\RogerBundle\Repository\RogerRepositoryTrait;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TaskTag>
 *
 * @method TaskTag|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskTag|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskTag[]    findAll()
 * @method TaskTag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskTagRepository extends ServiceEntityRepository implements RogerRepositoryInterface
{
	use RogerRepositoryTrait;
	
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, TaskTag::class);
	}

}
