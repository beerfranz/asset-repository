<?php

namespace Beerfranz\RogerBundle\ApiResource;

use Beerfranz\RogerBundle\Entity\RogerEntity;

class RogerApiResource extends RogerEntity implements RogerApiResourceInterface
{

	public function fromEntityToApi($entity)
	{
		$normalizedEntity = $this->getSerializer()->normalize($entity, 'array');
		$this->hydrator($normalizedEntity);

		return $this;
	}

	public function fromApiToEntity($entity)
	{
		$entity->hydrator($this->getSerializer()->normalize($this, 'array'));

		return $entity;
	}
}
