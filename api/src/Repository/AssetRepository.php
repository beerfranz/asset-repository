<?php

namespace App\Repository;

use App\Entity\Asset;
use App\Entity\Source;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Asset>
 *
 * @method Asset|null find($id, $lockMode = null, $lockVersion = null)
 * @method Asset|null findOneBy(array $criteria, array $orderBy = null)
 * @method Asset[]    findAll()
 * @method Asset[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssetRepository extends ServiceEntityRepository implements RogerRepositoryInterface
{
    use RogerRepositoryTrait;
    
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Asset::class);
    }

    public function save(Asset $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Asset $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // additionalConditions = [ 'user' => $user, 'source' => $source ]
    public function findAssetsByidentifiersNotIn(array $identifiers, array $additionalConditions = []): Paginator
    {
        $query = $this->createQueryBuilder('a')
           ->andWhere('a.identifier not in (:identifiers)')
           ->setParameter('identifiers', $identifiers)
           ->orderBy('a.id', 'ASC')
        ;

        foreach ($additionalConditions as $key => $value)
        {
            $query->andWhere('a.' . $key . ' = :' . $key)->setParameter($key, $value);
        }

        return new Paginator($query->getQuery(), $fetchJoinCollection = true);

        // return $query->getQuery()->getResult();
    }

   public function findOneByIdentifier($value): ?Asset
   {
        return $this->createQueryBuilder('a')
           ->andWhere('a.identifier = :val')
           ->setParameter('val', $value)
           ->getQuery()
           ->getOneOrNullResult()
        ;
   }

    public function deleteBySourceAndIdentifiersNotIn(Source $source, array $identifiers): bool
    {
        $query = $this->createQueryBuilder('a')
                      ->delete()
                      ->where('a.source = :source')
                      ->setParameter('source', $source->getId())
                      ->andWhere('a.identifier not in (:identifiers)')
                      ->setParameter('identifiers', $identifiers)
                      ->getQuery();

        return $query->getResult();
    }

    public function findRules(): Array
    {
        return $this->createQueryBuilder('a')
           ->select('a.rules')
           ->addSelect('a.id')
           ->andWhere('a.rules IS NOT NULL')
           ->getQuery()
           ->getResult()
        ;
    }

    public function countAssets(): Int
    {
        return $this->createQueryBuilder('a')
           ->select('count(a.id)')
           ->getQuery()
           ->getSingleScalarResult()
        ;
    }

}
