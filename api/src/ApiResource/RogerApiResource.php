<?php

namespace App\ApiResource;

use App\Entity\RogerEntity;

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class RogerApiResource extends RogerEntity
{
  protected function getSerializer(): Serializer
  {
    $normalizers = [new ObjectNormalizer()];
    $serializer = new Serializer($normalizers, []);

    return $serializer;
  }

  public function fromEntityToApi($entity)
  {
    $normalizedEntity = $this->getSerializer()->normalize($entity, 'array');
    $this->hydrator($normalizedEntity);

    return $this;
  }

  public function fromApiToEntity($entity)
  {
    $entity->hydrator($this->getSerializer()->normalize($this, 'array'));

    return $entity;
  }
}
