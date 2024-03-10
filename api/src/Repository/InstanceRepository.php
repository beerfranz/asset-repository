<?php

namespace App\Repository;

use App\Entity\Instance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * @extends ServiceEntityRepository<Instance>
 *
 * @method Instance|null find($id, $lockMode = null, $lockVersion = null)
 * @method Instance|null findOneBy(array $criteria, array $orderBy = null)
 * @method Instance[]    findAll()
 * @method Instance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InstanceRepository extends ServiceEntityRepository implements RogerRepositoryInterface
{
    use RogerRepositoryTrait;
    
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Instance::class);
    }

    public function save(Instance $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Instance $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findInstancesByIdentifiersNotIn(array $identifiers, array $additionalConditions = []): array
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
    
    public function countInstances(): Int
    {
        return $this->createQueryBuilder('i')
           ->select('count(i.id)')
           ->getQuery()
           ->getSingleScalarResult()
        ;
    }

    public function countInstancesValidated(): Int
    {
        return $this->createQueryBuilder('i')
           ->select('count(i.id)')
           ->andWhere('i.isConform = true')
           ->getQuery()
           ->getSingleScalarResult()
        ;
    }

    public function countInstancesReconcilied(): Int
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('total', 'total');
        $result = $this->getEntityManager()->createNativeQuery("SELECT COUNT(id) as total FROM instance WHERE asset_id is not null;", $rsm)
           ->getResult()
        ;
        return $result[0]['total'];
    }

    public function countInstancesTotalChecks(): int
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('total', 'total');
        $result = $this->getEntityManager()->createNativeQuery("SELECT SUM(cast(conformities->'statistics'->>'total' as integer)) as total FROM instance WHERE asset_id is not null;", $rsm)
           ->getResult()
        ;
        return $result[0]['total'];
    }

    public function countInstancesTotalErrors(): int
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('total', 'total');
        $result = $this->getEntityManager()->createNativeQuery("SELECT SUM(cast(conformities->'statistics'->>'errors' as integer)) as total FROM instance WHERE asset_id is not null;", $rsm)
           ->getResult()
        ;
        return $result[0]['total'];
    }

}
