<?php

namespace App\Entity;

class Trigger extends RogerEntity {

  const ALLOWED_LEVELS = [ 'info', 'success', 'warning', 'danger' ];

  protected ?string $printLevel = 'info';

  protected array $rules = [];

  public function setPrintLevel(string $level): self
  {
    if (! in_array($level, self::ALLOWED_LEVELS))
      throw new \Exception('Level "' . $level . '" not authorized. Use info, success, warning or danger.');
    
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
