<?php

namespace App\Entity;

class Trigger extends RogerEntity {

  protected ?string $printLevel = 'info';

  protected array $rules = [];

  public function setPrintLevel(string $level): self
  {
    $this->printLevel = $level;
    return $this;
  }

  public function getPrintLevel(): string
  {
    return $this->printLevel;
  }

  public function getRules(): array
  {
    return $this->rules;
  }
}
