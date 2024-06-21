<?php

namespace Beerfranz\RogerBundle\Entity;

interface RogerEntityInterface
{
	public function hydrator(array $data = []): self;

	public function __get($name);

	public function __set($name, $value);

	public function __getMessengerSerializationGroups(): ?array;

	public function __getMessengerClass(): string;

	public function __getSequenceClass(): ?string;

	public function __getSequencedProperties(): array;
}
