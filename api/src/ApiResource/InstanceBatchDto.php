<?php

namespace App\ApiResource;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class InstanceBatchDto
{
    #[Assert\All([new Assert\Collection(
        fields: [
            'identifier' => new Assert\Required([
                new Assert\NotBlank
            ]),
            'asset' => new Assert\Collection(
                fields: [ 'identifier' => new Assert\Required(new Assert\NotBlank) ],
                allowMissingFields: true
            ),
        ],
        allowExtraFields: true,
        allowMissingFields: true
    )])]
    #[Groups(['Instance:read', 'Instance:write'])]
    public array $instances = [];

}
