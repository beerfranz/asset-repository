<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

// use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;

trait RogerRepositoryTrait {

   public function rogerFindBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
   {
       $q = $this->createQueryBuilder('s')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
        ;

        $rootAlias = $q->getRootAliases()[0];

        foreach($orderBy as $key => $sens)
        {
            $q->addOrderBy($rootAlias . '.' . $key, $sens);
        }

        foreach ($criteria as $filter => $value) {
            $this->addFilter($q, $filter, $value, $rootAlias);
        }

        return $q->getQuery()->getResult();
   }

    public function findOneByName($value): ?Object
    {
        return $this->createQueryBuilder('s')
           ->andWhere('s.name = :val')
           ->setParameter('val', $value)
           ->getQuery()
           ->getOneOrNullResult()
        ;
    }

    public function findOneByIdentifier($value): ?Object
    {
        return $this->createQueryBuilder('s')
           ->andWhere('s.identifier = :val')
           ->setParameter('val', $value)
           ->getQuery()
           ->getOneOrNullResult()
        ;
    }

    protected function isPartial($filter) {
        if (1 === preg_match('/^(.*)_partial$/', $filter, $matches)) {
            return $matches[1];
        }
        return false;
    }

    protected function getProperty($filter) {
        if (1 === preg_match('/^(.*)_partial$/', $filter, $matches)) {
            return $matches[1];
        }
        return $filter;
    }

    protected function addFilter($queryBuilder, $filter, $value, $rootAlias) {

        if ($property = $this->isPartial($filter)) {
            $field = $rootAlias . '.' . $property;
            $queryBuilder->andWhere($field . ' LIKE :' . $filter)
                         ->setParameter($filter, '%' . $value . '%');
        } elseif ($filter == 'groups') {
            $queryBuilder->select(null);
            foreach($value as $group) {
                if (1 === preg_match('/^.*:(.*)$/', $group, $matches)) {
                    $field = $rootAlias . '.' . $matches[1];
                    $queryBuilder->groupBy($field);
                    $queryBuilder->orderBy($field, 'ASC');
                    $queryBuilder->select($field);
                }
            }
        } else {
            $field = $rootAlias . '.' . $filter;
            $queryBuilder->andWhere($field . ' = :' . $filter)
                         ->setParameter($filter, $value);
        }
    }

}