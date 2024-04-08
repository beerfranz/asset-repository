<?php

namespace App\Service;

use App\Entity\RogerEntityInterface;

interface RogerServiceInterface
{
  public function newEntity(): RogerEntityInterface;
}
