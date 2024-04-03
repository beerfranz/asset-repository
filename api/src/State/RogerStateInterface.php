<?php

namespace App\State;

use App\Entity\RogerEntity as Entity;
use App\ApiResource\RogerApi as Api;

interface RogerStateInterface
{
    public function processOneEntity(mixed $data);

    public function initEntity(mixed $data): Entity;

    public function upsertEntity(Entity $entity, mixed $data): Entity;

}
