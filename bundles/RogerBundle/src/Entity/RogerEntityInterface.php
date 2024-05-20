<?php

namespace Beerfranz\RogerBundle\Entity;

interface RogerEntityInterface
{
	public function hydrator(array $data = []): self;

	public function __get($name);

	public function __set($name, $value);
}
