<?php

namespace App\Repository;

use App\Entity\AssetDefinition;

use Beerfranz\RogerBundle\Repository\RogerRepositoryInterface;
use Beerfranz\RogerBundle\Repository\RogerRepositoryTrait;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AssetDefinition>
 *
 * @method AssetDefinition|null find($id, $lockMode = null, $lockVersion = null)
 * @method AssetDefinition|null findOneBy(array $criteria, array $orderBy = null)
 * @method AssetDefinition[]    findAll()
 * @method AssetDefinition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssetDefinitionRepository extends ServiceEntityRepository implements RogerRepositoryInterface
{
    use RogerRepositoryTrait;
    
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AssetDefinition::class);
    }

    public function save(AssetDefinition $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AssetDefinition $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneByIdentifier($value): ?AssetDefinition
    { 
        return $this->createQueryBuilder('a')
                    ->andWhere('a.identifier = :val')
                    ->setParameter('val', $value)
                    ->getQuery()
                    ->getOneOrNullResult()
        ;
    }

    public function findAssetDefinitionsByidentifiersNotIn(array $identifiers, array $additionalConditions = []): array
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

}
