<?php

namespace App\Assets\Entity;

use App\Assets\Repository\RelationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RelationRepository::class)]
class Relation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $kind = null;

    #[ORM\ManyToOne(inversedBy: 'relations')]
    private ?Source $source = null;

    #[ORM\Column(nullable: true)]
    private array $attributes = [];

    #[ORM\ManyToOne(inversedBy: 'fromRelations')]
    private ?Asset $fromAsset = null;

    #[ORM\ManyToOne(inversedBy: 'toRelations')]
    private ?Asset $toAsset = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKind(): ?string
    {
        return $this->kind;
    }

    public function setKind(string $kind): self
    {
        $this->kind = $kind;

        return $this;
    }

    public function getSource(): ?Source
    {
        return $this->source;
    }

    public function setSource(?Source $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(?array $attributes): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function getFromAsset(): ?Asset
    {
        return $this->fromAsset;
    }

    public function setFromAsset(?Asset $fromAsset): self
    {
        $this->fromAsset = $fromAsset;

        return $this;
    }

    public function getToAsset(): ?Asset
    {
        return $this->toAsset;
    }

    public function setToAsset(?Asset $toAsset): self
    {
        $this->toAsset = $toAsset;

        return $this;
    }

}
