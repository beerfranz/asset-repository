<?php

namespace Beerfranz\RogerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Index(fields: ["isDone"])]
trait RogerEnabledTrait {

	#[ORM\Column()]
	#[Groups(['Roger:Messenger'])]
	private ?bool $enabled = false;

	public function isEnabled(): ?bool
	{
		return $this->enabled;
	}

	public function setEnabled(bool $enabled): static
	{
		$this->enabled = $enabled;

		return $this;
	}
}
