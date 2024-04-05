<?php

namespace App\Entity;

use JsonSerializable;

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Serializer;

abstract class RogerEntity implements RogerEntityInterface, JsonSerializable {
  
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
  
  protected function getSerializer(): Serializer
  {
    $defaultContext = [
      AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function (object $object, string $format, array $context): string {
        return $object->getId();
      },
    ];
    $normalizers = [new DateTimeNormalizer, new ObjectNormalizer(null, null, null, null, null, null, $defaultContext)];
    $serializer = new Serializer($normalizers, []);

    return $serializer;
  }

  public function __set($name, $value)
  {

    $methodName = 'set' . ucfirst($name);
    
    if (method_exists($this, $methodName)) {
      $this->$methodName($value);
      return $this;
    }
    
    if (property_exists($this, $name)) {
      try {
        $this->$name = $value;
      } catch (\Exception $e) {
        return $this;
      }

      return $this;
    }

    return $this;
    // throw new \Exception('The property ' . $name . ' not exists, or no method ' . $methodName());
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

  public function toArray(): array
  {
    return $this->getSerializer()->normalize($this, 'array');
  }

}
