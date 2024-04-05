<?php

namespace App\Entity;

class TaskWorkflowStatus extends RogerEntity {

  protected string $status = 'todo';

  protected array $constraints = [];

  protected array $nextStatuses = [];

  protected bool $isDone = false;

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
}
