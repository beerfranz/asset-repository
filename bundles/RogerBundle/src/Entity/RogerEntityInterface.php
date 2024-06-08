<?php

namespace Beerfranz\RogerBundle\Entity;

interface RogerEntityInterface
{
	public function hydrator(array $data = []): self;

	public function __get($name);

	public function __set($name, $value);

	public function getMessengerSerializationGroup(): ?string;

	public function getMessengerClass(): string;

	public function getSequenceClass(): ?string;

	public function getSequencedProperties(): array;
}
