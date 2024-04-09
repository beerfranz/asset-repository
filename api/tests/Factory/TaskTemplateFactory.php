<?php

namespace App\Tests\Factory;

use App\Entity\TaskTemplate;

final class TaskTemplateFactory extends RogerFactory
{

    protected static function getClass(): string
    {
        return TaskTemplate::class;
    }

    protected function getDefaults(): array
    {
        
        return [
            'identifier' => $this->randomString(),
            'title' => $this->randomString(),
            'description' => $this->randomString(),
        ];
    }

}
