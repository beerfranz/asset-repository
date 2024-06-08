<?php

namespace App\Assets\Repository;

use App\Assets\Entity\Relation;
use App\Assets\Entity\Asset;
use App\Assets\Entity\Source;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Relation>
 *
 * @method Relation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Relation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Relation[]    findAll()
 * @method Relation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RelationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Relation::class);
    }

    public function save(Relation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Relation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
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

    public function findByUniq(Asset $from, Asset $to, string $kind)
    {
        $query = $this->createQueryBuilder('a')
            ->andWhere('a.fromAsset = :from_id')
            ->setParameter('from_id', $from->getId())
            ->andWhere('a.toAsset = :to_id')
            ->setParameter('to_id', $to->getId())
            ->andWhere('a.kind = :kind')
            ->setParameter('kind', $kind);


        return $query->getQuery()->getOneOrNullResult();
    }

    public function deleteBySourceAndIdsNotIn(Source $source, array $ids): bool
    {
        $query = $this->createQueryBuilder('a')
                      ->delete()
                      ->where('a.source = :source')
                      ->setParameter('source', $source->getId())
                      ->andWhere('a.id not in (:ids)')
                      ->setParameter('ids', $ids)
                      ->getQuery();

        return $query->getResult();
    }

//    /**
//     * @return Relation[] Returns an array of Relation objects
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

//    public function findOneBySomeField($value): ?Relation
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
