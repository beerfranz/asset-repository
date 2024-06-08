<?php

namespace Beerfranz\RogerBundle\ApiResource;

use Beerfranz\RogerBundle\Entity\RogerEntity;

class RogerApiResource extends RogerEntity implements RogerApiResourceInterface
{

	public function fromEntityToApi($entity)
	{
		$normalizedEntity = $this->__getSerializer()->normalize($entity, 'array');
		$this->hydrator($normalizedEntity);

		return $this;
	}

	public function fromApiToEntity($entity)
	{
		$entity->hydrator($this->__getSerializer()->normalize($this, 'array'));

		return $entity;
	}
}
