<?php

namespace App\Tasks\Entity;

use Beerfranz\RogerBundle\Entity\RogerEntity;

class TaskWorkflowStatus extends RogerEntity {

	protected string $status = 'todo';

	protected array $constraints = [];

	protected array $nextStatuses = [];

	protected bool $isDone = false;

	protected bool $isDefault = false;

	public function getConstraints(): array
	{
		return $this->constraints;
	}

	public function getNextStatuses(): array
	{
		return $this->nextStatuses;
	}

	public function getStatus(): string
	{
		return $this->status;
	}

	public function getIsDone(): bool
	{
		return $this->isDone;
	}

	public function getIsDefault(): bool
	{
		return $this->isDefault;
	}
}
