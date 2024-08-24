<?php

namespace Beerfranz\RogerBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

trait RogerRepositoryTrait {

	protected function getQueryBuilder() {
		return $this->createQueryBuilder('s');
	}

	public function rogerFindBy(array $criteria, array $orderBy = null, $limit = null, $offset = null, $paginator = false): array|Paginator
	{
		$q = $this->getQueryBuilder()
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

		$query = $q->getQuery();

		if ($paginator) {
			return new Paginator($query, $fetchJoinCollection = false);
		}

		return $query->getResult();
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

	protected function isSubResourceFilter($filter) {
		if (1 === preg_match('/^(.*)\.(.*)$/', $filter, $matches)) {
			return $matches[1];
		}
		return false;
	}

	protected function decodeSubResource(string $filter) {
		if (1 === preg_match('/^(.*)\.(.*)$/', $filter, $matches)) {
			return $matches;
		}
		return false;
	}

	protected function addFilter($queryBuilder, $filter, $value, $rootAlias) {
		
		if ($matches = $this->decodeSubResource($filter)) {
			$subResource = $matches[1];
			$alias = 't_' . uniqid();
			$queryBuilder->leftJoin($rootAlias . '.' . $subResource, $alias);
			$rootAlias = $alias;

			$filter = $matches[2];
		}

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

			if ($value === '') {
				$queryBuilder->andWhere($field . ' is NULL');
			} else {
				$queryBuilder->andWhere($field . ' = :' . $filter)
						 ->setParameter($filter, $value);
			}
		}
	}

}
