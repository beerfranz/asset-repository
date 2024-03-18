<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\RiskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: RiskRepository::class)]
#[ORM\UniqueConstraint(columns:["identifier"])]
#[ApiResource(
    normalizationContext: ['groups' => ['Risks:read']],
    denormalizationContext: ['groups' => ['Risks:write']],
    security: "is_granted('ASSET_READ')",
    routePrefix: '/entity',
)]
#[UniqueEntity(fields: ['identifier'], message: 'There is already a risk with this identifier')]
class Risk
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['Risks:read', 'Risks:write'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['Risks:read', 'Risks:write'])]
    private ?string $identifier = null;

    #[ORM\ManyToOne(inversedBy: 'risks')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['Risks:read', 'Risks:write'])]
    private ?RiskManager $riskManager = null;

    #[ORM\ManyToOne(inversedBy: 'risks')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['Risks:read', 'Risks:write'])]
    private ?Asset $asset = null;

    #[ORM\Column]
    #[Groups(['Risks:read', 'Risks:write'])]
    private array $values = [];

    #[ORM\Column(nullable: true)]
    #[Groups(['Risks:read', 'Risks:write'])]
    private ?array $mitigations = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['Risks:read', 'Risks:write'])]
    private ?string $description = null;

    private ?string $initialRisk = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): static
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getRiskManager(): ?RiskManager
    {
        return $this->riskManager;
    }

    public function setRiskManager(?RiskManager $riskManager): static
    {
        $this->riskManager = $riskManager;

        return $this;
    }

    public function getAsset(): ?Asset
    {
        return $this->asset;
    }

    public function setAsset(?Asset $asset): static
    {
        $this->asset = $asset;

        return $this;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function setValues(array $values): static
    {
        $this->values = $values;

        return $this;
    }

    public function getMitigations(): ?array
    {
        return $this->mitigations;
    }

    public function setMitigations(?array $mitigations): static
    {
        $this->mitigations = $mitigations;

        return $this;
    }

    #[Groups(['Risks:read'])]
    public function getInitialRisk(): string
    {
        return $this->initialRisk;
    }

    public function setInitialRisk(?string $initialRisk): self
    {
        $this->initialRisk = $initialRisk;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }
}
