<?php

namespace App\Tests\Factory;

use App\Entity\TaskType;

final class TaskTypeFactory extends RogerFactory
{

    protected static function getClass(): string
    {
        return TaskType::class;
    }

    protected function getDefaults(): array
    {
        return [
            'identifier' => $this->randomString(),
        ];
    }


}
