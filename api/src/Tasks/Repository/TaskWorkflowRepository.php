<?php

namespace App\Tasks\Repository;

use App\Tasks\Entity\TaskWorkflow;

use Beerfranz\RogerBundle\Repository\RogerRepositoryInterface;
use Beerfranz\RogerBundle\Repository\RogerRepositoryTrait;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TaskWorkflow>
 *
 * @method TaskWorkflow|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskWorkflow|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskWorkflow[]    findAll()
 * @method TaskWorkflow[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskWorkflowRepository extends ServiceEntityRepository implements RogerRepositoryInterface
{
	use RogerRepositoryTrait;
	
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, TaskWorkflow::class);
	}

//    /**
//     * @return TaskWorkflow[] Returns an array of TaskWorkflow objects
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

//    public function findOneBySomeField($value): ?TaskWorkflow
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
