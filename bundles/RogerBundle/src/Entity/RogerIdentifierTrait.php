<?php

namespace Beerfranz\RogerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

trait RogerIdentifierTrait {

	#[ORM\Column(length: 255, unique: true, nullable: false)]
	private ?string $identifier = null;

	public function getIdentifier(): ?string
	{
		return $this->identifier;
	}

	public function setIdentifier(string $identifier): self
	{
		$this->identifier = $identifier;

		return $this;
	}

}
