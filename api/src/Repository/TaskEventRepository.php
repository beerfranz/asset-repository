<?php

namespace App\Repository;

use App\Entity\TaskEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TaskEvent>
 *
 * @method TaskEvent|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskEvent|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskEvent[]    findAll()
 * @method TaskEvent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskEventRepository extends ServiceEntityRepository implements RogerRepositoryInterface
{
    use RogerRepositoryTrait;
    
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskEvent::class);
    }

//    /**
//     * @return TaskEvent[] Returns an array of TaskEvent objects
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

//    public function findOneBySomeField($value): ?TaskEvent
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
