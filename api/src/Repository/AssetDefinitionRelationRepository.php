<?php

namespace App\Repository;

use App\Entity\AssetDefinitionRelation;
use App\Entity\AssetDefinition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AssetDefinitionRelation>
 *
 * @method AssetDefinitionRelation|null find($id, $lockMode = null, $lockVersion = null)
 * @method AssetDefinitionRelation|null findOneBy(array $criteria, array $orderBy = null)
 * @method AssetDefinitionRelation[]    findAll()
 * @method AssetDefinitionRelation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssetDefinitionRelationRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AssetDefinitionRelation::class);
    }

    public function save(AssetDefinitionRelation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AssetDefinitionRelation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneByIdentifier(AssetDefinition $from, AssetDefinition $to, $name = null): ?AssetDefinitionRelation
    {
        $qb = $this->createQueryBuilder('a')
                   ->andWhere('a.assetDefinitionFrom = :from')
                   ->setParameter('from', $from->getId())
                   ->andWhere('a.assetDefinitionTo = :to')
                   ->setParameter('to', $to->getId())
        ;

        if ($name !== null) {
            $qb->andWhere('a.name = :name')
               ->setParameter('name', $name);
        }
        
        return $qb->getQuery()
                  ->getOneOrNullResult()
        ;
    }

    public function findRelationByIdsNotIn(array $ids, array $additionalConditions = []): array
    {
        $query = $this->createQueryBuilder('a')
           ->andWhere('a.id not in (:ids)')
           ->setParameter('ids', $ids)
           ->orderBy('a.id', 'ASC')
        ;

        foreach ($additionalConditions as $key => $value)
        {
            $query->andWhere('a.' . $key . ' = :' . $key)->setParameter($key, $value);
        }

        return $query->getQuery()->getResult();
    }

//    /**
//     * @return AssetDefinitionRelation[] Returns an array of AssetDefinitionRelation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AssetDefinitionRelation
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
