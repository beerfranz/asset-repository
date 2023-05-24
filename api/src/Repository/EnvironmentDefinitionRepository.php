<?php

namespace App\Repository;

use App\Entity\EnvironmentDefinition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EnvironmentDefinitions>
 *
 * @method EnvironmentDefinitions|null find($id, $lockMode = null, $lockVersion = null)
 * @method EnvironmentDefinitions|null findOneBy(array $criteria, array $orderBy = null)
 * @method EnvironmentDefinitions[]    findAll()
 * @method EnvironmentDefinitions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnvironmentDefinitionRepository extends ServiceEntityRepository implements RogerRepositoryInterface
{
    use RogerRepositoryTrait;
    
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EnvironmentDefinition::class);
    }

    public function save(EnvironmentDefinition $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EnvironmentDefinition $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneByIdentifier($value): ?EnvironmentDefinition
    {
        return $this->createQueryBuilder('e')
           ->andWhere('e.identifier = :val')
           ->setParameter('val', $value)
           ->getQuery()
           ->getOneOrNullResult()
        ;
    }
}
