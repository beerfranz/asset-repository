<?php

namespace Beerfranz\RogerBundle\Service;

use Beerfranz\RogerBundle\Entity\RogerEntityInterface;

interface RogerServiceInterface
{
	public function newEntity(): RogerEntityInterface;
}
