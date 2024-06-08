<?php

namespace Beerfranz\RogerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

trait RogerTitleTrait {

	#[ORM\Column(length: 100, nullable: false)]
	protected ?string $title = null;

	public function getTitle(): ?string
	{
		return $this->title;
	}

	public function setTitle(string $title): self
	{
		$this->title = $title;

		return $this;
	}

}
