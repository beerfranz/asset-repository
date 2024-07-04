<?php

namespace Beerfranz\RogerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Index(fields: ["tenant"])]
trait RogerAttributesTrait {

	#[Groups(['Roger:Messenger'])]
	#[ORM\Column(nullable: true)]
	private ?array $attributes = null;

	public function getAttributes(): ?array
	{
		return $this->attributes;
	}

	public function setAttributes(?array $attributes): static
	{
		$this->attributes = $attributes;

		return $this;
	}
}
