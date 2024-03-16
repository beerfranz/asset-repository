<?php

namespace App\Repository;

use App\Entity\RiskManager;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RiskManager>
 *
 * @method RiskManager|null find($id, $lockMode = null, $lockVersion = null)
 * @method RiskManager|null findOneBy(array $criteria, array $orderBy = null)
 * @method RiskManager[]    findAll()
 * @method RiskManager[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RiskManagerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RiskManager::class);
    }

//    /**
//     * @return RiskManager[] Returns an array of RiskManager objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?RiskManager
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
