<?php

namespace App\Entity;

class TaskWorkflowStatus extends RogerEntity {

  protected string $status = 'todo';

  protected array $constraints = [];

  protected array $next_statuses = [];

  protected bool $isDone = false;

  public function getConstraints(): array
  {
    return $this->constraints;
  }

  public function getNextStatuses(): array
  {
    return $this->next_statuses;
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
