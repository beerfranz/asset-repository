<?php

namespace App\Entity;

use App\Repository\AssetDefinitionRelationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AssetDefinitionRelationRepository::class)]
class AssetDefinitionRelation
{
    const RELATION_KINDS = [
        'group' => [ 'isGroup' => true ] , 'groupedBy' => [ 'inversed' => true ],
        'host' => [ 'isGroup' => true ], 'hostedBy' => [ 'inversed' => true ],
        'package' => [ 'isGroup' => true ], 'packagedBy' => [ 'inversed' => true ],
        'connect' => [ 'isGroup' => false ], 'connected' => [ 'inversed' => true ],
        'data' => [ 'isGroup' => false ],
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['AssetDefinition:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'relationsFrom', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['AssetDefinition:read'])]
    private ?AssetDefinition $assetDefinitionFrom = null;

    #[ORM\ManyToOne(inversedBy: 'relationsTo', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['AssetDefinition:read'])]
    private ?AssetDefinition $assetDefinitionTo = null;

    #[ORM\Column(length: 255)]
    #[Groups(['AssetDefinition:read'])]
    private ?string $name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAssetDefinitionFrom(): ?AssetDefinition
    {
        return $this->assetDefinitionFrom;
    }

    public function setAssetDefinitionFrom(?AssetDefinition $assetDefinitionFrom): self
    {
        $this->assetDefinitionFrom = $assetDefinitionFrom;

        return $this;
    }

    public function getAssetDefinitionTo(): ?AssetDefinition
    {
        return $this->assetDefinitionTo;
    }

    public function setAssetDefinitionTo(?AssetDefinition $assetDefinitionTo): self
    {
        $this->assetDefinitionTo = $assetDefinitionTo;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    #[Groups(['AssetDefinition:read'])]
    public function isGroup(): bool
    {
        if (self::RELATION_KINDS[$this->name]['isGroup'])
            return true;
        else
            return false;
    }
}
