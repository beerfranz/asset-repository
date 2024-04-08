<?php

namespace App\State;

use App\Entity\RogerEntityInterface;
use App\ApiResource\RogerApiResourceInterface;

interface RogerStateInterface
{
  public function newApi(): RogerApiResourceInterface;

  public function fromApiToEntity($api, $entity): RogerEntityInterface;

  public function fromEntityToApi($entity, $api): RogerApiResourceInterface;
}
