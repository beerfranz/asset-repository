<?php

namespace App\Common\Entity;

class UserTemplateType {

  protected ?string $result = null;

  protected ?string $error = null;

  public function __construct(?string $result = null, ?string $error = null)
  {
    $this->setResult($result);
    $this->setError($error);

    return $this;
  }

  public function getResult(): string
  {
    if ($this->hasError())
      throw new \Exception($this->getError());

    return $this->result;
  }

  public function setResult($result): self
  {
    $this->result = $result;
    return $this;
  }

  public function getError(): string
  {
    return $this->error;
  }

  public function setError($error): self
  {
    $this->error = $error;
    return $this;
  }

  public function hasError(): bool
  {
    return $this->error === null ? false : true;
  }

  public function getBoolResult(): bool
  {
    $test = $this->getResult();
    if ($test === 'true')
      return true;
    elseif ($test === 'false')
      return false;
    else {
      throw new \Exception($this->getError());
    }
  }
}