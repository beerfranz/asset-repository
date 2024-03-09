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

    private array $conformity = [];

    #[ORM\ManyToOne(inversedBy: 'instances', cascade: ['persist'])]
    #[Groups(['Instance:read', 'Instance:write'])]
    private ?Source $source = null;

    #[ORM\ManyToOne(inversedBy: 'instances')]
    #[Groups(['Instance:read', 'Instance:write', 'Kind:read'])]
    private ?Kind $kind = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['Instance:read', 'Instance:write'])]
    private ?string $friendlyName = null;

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

    #[Groups(['Instance:read'])]
    public function getConformity(): bool
    {
        $this->conformity = [ 'error' => [], 'validated' => []];

        // Conformity v1: only check version
        // if ($this->asset !== null) {
        //     $assetVersion = $this->asset->getVersion() === null ? null : $this->asset->getVersion()->getName();
        // } else {
        //     $assetVersion = null;
        // }
        
        // if ($assetVersion !== $this->version)
        //     $this->conformity['version'] = [ 'assetData' => $assetVersion ];

        // Conformity v2: check all attributes
        if ($this->asset !== null) {
            foreach ($this->asset->getAttributes() as $category => $attributes) {
                foreach ($attributes as $attribute => $constraint) {
                    if (isset($this->attributes[$category][$attribute])) {
                        $attributeValue = $this->attributes[$category][$attribute];
                        preg_match('/^([^ ]*)(.*)$/', $constraint, $matches);
                        
                        if ($matches[1] == 'in' && isset($matches[2]))
                            $check = in_array($attributeValue, json_decode($matches[2]));
                        else
                            $check = $attributeValue === $matches[0];

                        if ($check)
                            $this->conformity['validated']['attributes'][$category][$attribute] = [ 'expected' => $constraint ];
                        else
                            $this->conformity['error']['attributes'][$category][$attribute] = [ 'expected' => $constraint ];
                    } 
                    else
                        $this->conformity['error']['attributes'][$category][$attribute] = [ 'expected' => $constraint ];
                }
            }
        } else {
            return false;
        }

        return count($this->conformity['error']) === 0 ? true : false;
    }

    #[Groups(['Instance:read'])]
    public function getConformityDetails(): array
    {
        return $this->conformity;
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
}
