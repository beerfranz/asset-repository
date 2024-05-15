<?php

namespace Beerfranz\RogerBundle\State;

use Beerfranz\RogerBundle\Entity\RogerEntityInterface;
use Beerfranz\RogerBundle\ApiResource\RogerApiResourceInterface;

interface RogerStateInterface
{
	public function newApi(): RogerApiResourceInterface;

	public function fromApiToEntity($api, $entity): RogerEntityInterface;

	public function fromEntityToApi($entity, $api): RogerApiResourceInterface;
}
