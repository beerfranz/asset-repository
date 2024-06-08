<?php

namespace Beerfranz\RogerBundle\Entity;

use Beerfranz\RogerBundle\Entity\RogerIdTrait;

use Doctrine\ORM\Mapping as ORM;

class RogerSequence
{
	use RogerIdTrait;

	#[ORM\Column]
	protected int $sequenceNumber;

	public function __construct() {
		$this->sequenceNumber = 1;
	}

	public function getSequenceNumber(): int
	{
		return $this->sequenceNumber;
	}
}
