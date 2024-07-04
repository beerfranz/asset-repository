<?php

namespace Beerfranz\RogerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Index(fields: ["tenant"])]
trait RogerTenantTrait {

	#[ORM\Column()]
	#[Groups(['Roger:Messenger'])]
	private ?int $tenant = null;

	public function getTenant(): ?int
	{
		return $this->tenant;
	}

	public function setTenant(int $tenant): static
	{
		$this->tenant = $tenant;

		return $this;
	}
}
