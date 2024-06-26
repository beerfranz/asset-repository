<?php

namespace App\Tasks\Repository;

use App\Tasks\Entity\TaskType;

use Beerfranz\RogerBundle\Repository\RogerRepositoryInterface;
use Beerfranz\RogerBundle\Repository\RogerRepositoryTrait;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TaskType>
 *
 * @method TaskType|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskType|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskType[]    findAll()
 * @method TaskType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskTypeRepository extends ServiceEntityRepository implements RogerRepositoryInterface
{
	use RogerRepositoryTrait;

	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, TaskType::class);
	}

	//    /**
	//     * @return TaskType[] Returns an array of TaskType objects
	//     */
	//    public function findByExampleField($value): array
	//    {
	//        return $this->createQueryBuilder('t')
	//            ->andWhere('t.exampleField = :val')
	//            ->setParameter('val', $value)
	//            ->orderBy('t.id', 'ASC')
	//            ->setMaxResults(10)
	//            ->getQuery()
	//            ->getResult()
	//        ;
	//    }

	//    public function findOneBySomeField($value): ?TaskType
	//    {
	//        return $this->createQueryBuilder('t')
	//            ->andWhere('t.exampleField = :val')
	//            ->setParameter('val', $value)
	//            ->getQuery()
	//            ->getOneOrNullResult()
	//        ;
	//    }
}
