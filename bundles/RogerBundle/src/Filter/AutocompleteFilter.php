<?php

namespace Beerfranz\RogerBundle\Filter;

use Beerfranz\RogerBundle\Repository\RogerRepositoryTrait;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;

final class AutocompleteFilter extends AbstractFilter
{
	use RogerRepositoryTrait;

	protected function filterProperty(string $filter, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
	{

		$property = $this->getProperty($filter);

		// otherwise filter is applied to order and page as well
		if (
			!$this->isPropertyEnabled($property, $resourceClass) ||
			!$this->isPropertyMapped($property, $resourceClass)
		) {
			return;
		}

		$rootAlias = $queryBuilder->getRootAliases()[0];
		
		$this->addFilter($queryBuilder, $filter, $value, $rootAlias);
	}

	// This function is only used to hook in documentation generators (supported by Swagger and Hydra)
	public function getDescription(string $resourceClass): array
	{
		if (!$this->properties) {
			return [];
		}

		$description = [];
		foreach ($this->properties as $property => $strategy) {
			$description[$this->encodeProperty($property)] = [
				// 'property' => $property,
				'property' => null,
				'type' => Type::BUILTIN_TYPE_STRING,
				'required' => false,
				'description' => 'Partial research',
				'openapi' => [
					'example' => 'foo',
					'allowReserved' => false,// if true, query parameters will be not percent-encoded
					'allowEmptyValue' => true,
					'explode' => false, // to be true, the type must be Type::BUILTIN_TYPE_ARRAY, ?product=blue,green will be ?product=blue&product=green
				],
			];
		}

		return $description;
	}

	protected function encodeProperty($property) {
		return $property. '_partial';
	}
}