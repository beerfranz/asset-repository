<?php

namespace Beerfranz\RogerBundle\Message;

abstract class RogerMessage
{

	public function __construct(
		protected string $event,
		protected array $context,
	)
	{

	}

	public function getEvent(): string
	{
		return $this->event;
	}

	public function getContext(): array
	{
		return $this->context;
	}

}
