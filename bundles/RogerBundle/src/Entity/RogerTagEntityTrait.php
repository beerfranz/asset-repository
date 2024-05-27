<?php

/**
 * Usage:
 * #[AssociationOverrides([
		new AssociationOverride('bar', [
			'joinColumns' => new JoinColumn([
				'name' => 'example_entity_overridden_bar_id',
				'referencedColumnName' => 'id',
			]),
		]),
	])]
 **/

namespace Beerfranz\RogerBundle\Entity;

use Beerfranz\RogerBundle\Entity\RogerEntity;
use Beerfranz\RogerBundle\Entity\RogerIdTrait;
use Beerfranz\RogerBundle\Entity\RogerIdentifierTrait;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\UniqueConstraint(columns:["name","value"])]
trait RogerTagEntityTrait
{

	#[ORM\Column(length: 255, nullable: false)]
	private ?string $name = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $value = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $color = null;

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(string $name): self
	{
		$this->name = $name;

		return $this;
	}

	public function getValue(): ?string
	{
		return $this->value;
	}

	public function setValue(string $value): self
	{
		$this->value = $value;

		return $this;
	}

	public function getColor(): ?string
	{
		return $this->color;
	}

	public function setColor(string $color): self
	{
		$this->color = $color;

		return $this;
	}

}
