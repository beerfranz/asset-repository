<?php

namespace App\Repository;

use App\Entity\IndicatorValue;
use App\Entity\Indicator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<IndicatorValue>
 *
 * @method IndicatorValue|null find($id, $lockMode = null, $lockVersion = null)
 * @method IndicatorValue|null findOneBy(array $criteria, array $orderBy = null)
 * @method IndicatorValue[]    findAll()
 * @method IndicatorValue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IndicatorValueRepository extends ServiceEntityRepository implements RogerRepositoryInterface
{
    use RogerRepositoryTrait;
    
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IndicatorValue::class);
    }

    public function findOneByIndicatorAndIdentifier(Indicator $indicator, $valueIdentifier)
    {
        return $this->createQueryBuilder('s')
           ->andWhere('s.identifier = :valueIdentifier')
           ->setParameter('valueIdentifier', $valueIdentifier)
           ->andWhere('s.indicator = :indicator')
           ->setParameter('indicator', $indicator)
           ->getQuery()
           ->getOneOrNullResult()
        ;
    }

    public function findIndicatorSample(Indicator $indicator): array
    {
        return $this->createQueryBuilder('s')
           ->andWhere('s.indicator = :indicator')
           ->setParameter('indicator', $indicator)
           ->setMaxResults(3)
           ->addOrderBy('s.identifier', 'DESC')
           ->getQuery()
           ->getResult()
        ;
    }

//    /**
//     * @return IndicatorValue[] Returns an array of IndicatorValue objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?IndicatorValue
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
