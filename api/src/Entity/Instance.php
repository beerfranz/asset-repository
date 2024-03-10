<?php

namespace App\Entity;

use App\Filter\AutocompleteFilter;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;

use ApiPlatform\Serializer\Filter\GroupFilter;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\InstanceRepository;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: InstanceRepository::class)]
#[ApiFilter(OrderFilter::class, properties: ['id', 'identifier', 'version'])]
#[ApiFilter(AutocompleteFilter::class, properties: [ 'identifier', 'kind.identifier'])]
#[ApiFilter(GroupFilter::class, arguments: ['parameterName' => 'groups', 'overrideDefaultGroups' => true, 'whitelist' => ['Instance:identifier', 'Kind:identifier']])]
#[ApiResource(
    normalizationContext: ['groups' => ['Instance:read']],
    denormalizationContext: ['groups' => ['Instance:write']],
    security: "is_granted('ASSET_READ')",
    routePrefix: '/entity',
)]
class Instance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['Instance:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['Instance:read', 'Instance:write', 'Instance:identifier', 'Asset:read'])]
    private ?string $identifier = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['Instance:read', 'Instance:write'])]
    private array $attributes = [];

    #[ORM\Column(length: 255)]
    #[Groups(['Instance:read', 'Instance:write'])]
    private ?string $version = null;

    #[ORM\ManyToOne(inversedBy: 'instances')]
    #[Groups(['Instance:read', 'Instance:write'])]
    private ?Asset $asset = null;

    #[ORM\ManyToOne(inversedBy: 'instances', cascade: ['persist'])]
    #[Groups(['Instance:read', 'Instance:write'])]
    private ?Source $source = null;

    #[ORM\ManyToOne(inversedBy: 'instances')]
    #[Groups(['Instance:read', 'Instance:write', 'Kind:read'])]
    private ?Kind $kind = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['Instance:read', 'Instance:write'])]
    private ?string $friendlyName = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['Instance:read'])]
    private ?bool $isConform = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['Instance:read'])]
    private ?array $conformities = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

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

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getAsset(): ?Asset
    {
        return $this->asset;
    }

    public function setAsset(?Asset $asset): self
    {
        $this->asset = $asset;

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

    public function getKind(): ?Kind
    {
        return $this->kind;
    }

    public function setKind(?Kind $kind): static
    {
        $this->kind = $kind;

        return $this;
    }

    public function getFriendlyName(): ?string
    {
        return $this->friendlyName;
    }

    public function setFriendlyName(?string $friendlyName): static
    {
        $this->friendlyName = $friendlyName;

        return $this;
    }

    public function getKindIdentifier(): ?string
    {
        try {
            return $this->getKind()->getIdentifier();
        } catch(\Error $e) {
            return null;
        }
    }

    public function isIsConform(): ?bool
    {
        return $this->isConform;
    }

    public function setIsConform(?bool $isConform): static
    {
        $this->isConform = $isConform;

        return $this;
    }

    public function getConformities(): ?array
    {
        return $this->conformities;
    }

    public function setConformities(?array $conformities): static
    {
        $this->conformities = $conformities;

        return $this;
    }

    public function getConformitiesByChecks(): ?array
    {
        return array_merge_recursive($this->conformities['errors'], $this->conformities['validated']);
    }
}
