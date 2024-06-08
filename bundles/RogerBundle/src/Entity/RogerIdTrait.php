<?php

namespace Beerfranz\RogerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

// https://github.com/doctrine/orm/issues/8893

trait RogerIdTrait {

	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: "IDENTITY")]
	#[ORM\Column]
	protected ?int $id = null;

	public function getId(): ?int
	{
		return $this->id;
	}

}
