<?php

namespace App\Entity;

use JsonSerializable;

abstract class RogerEntity implements JsonSerializable {
  
  public function __construct(array $data = []) {
    $this->hydrator($data);
  }
  
  public function hydrator(array $data = []): self
  {
    foreach ($data as $name => $value) {
      $this->__set($name, $value);
    }

    return $this;
  }
  
  public function __set($name, $value)
  {

    $methodName = 'set' . ucfirst($name);
    
    if (method_exists($this, $methodName)) {
      $this->$methodName($value);
      return $this;
    }
    
    if (property_exists($this, $name)) {
      $this->$name = $value;
      return $this;
    }
        
    throw new \Exception('The property ' . $name . ' not exists, or no method ' . $methodName());
  }
  
  public function __get($name) {
    $methodName = 'get' . ucfirst($name);
    
    if (method_exists($this, $methodName)) {
      return $this->$methodName();
    }
    
    if (property_exists($this, $name)) {
      return $this->$name;
    }
    
    throw new \Exception('The property ' . $name . ' not exists, or no method ' . $methodName());
  }

  public function jsonSerialize(): mixed
  {
    $result = array();

    foreach (get_object_vars($this) as $name => $value)
    {
      $result[$name] = $this->__get($name);
    }
    return $result;
  }

}