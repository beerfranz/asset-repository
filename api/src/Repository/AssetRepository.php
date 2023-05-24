<?php

namespace App\Repository;

use App\Entity\Asset;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

   /**
    * @return Asset[] Returns an array of Asset objects
    */
   public function findUserAssetsByIdentifiersNoIn($user, array $identifiers): array
   {
        return $this->createQueryBuilder('a')
           ->andWhere('a.identifier not in (:identifiers)')
           ->setParameter('identifiers', $identifiers)
           ->andWhere('a.createdBy = :user')
           ->setParameter('user', $user)
           ->orderBy('a.id', 'ASC')
           ->getQuery()
           ->getResult()
        ;
   }

    // additionalConditions = [ 'user' => $user, 'source' => $source ]
    public function findAssetsByidentifiersNotIn(array $identifiers, array $additionalConditions = []): array
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

        return $query->getQuery()->getResult();
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

}
