<?php

namespace App\Entity;

use App\Filter\AutocompleteFilter;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;

use App\Repository\AssetAuditRepository;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AssetAuditRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection()
    ],
    security: "is_granted('ASSET_READ')",
    normalizationContext: ['groups' => ['AssetAudit:read']],
    denormalizationContext: ['groups' => ['AssetAudit:write']],
)]
#[ApiFilter(OrderFilter::class, properties: ['id', 'datetime', 'actor', 'action', 'subject'])]
#[ApiFilter(AutocompleteFilter::class, properties: [ 'subject'])]
class AssetAudit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['AssetAudit:read', 'Asset:read', 'Asset:write'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['AssetAudit:read', 'AssetAudit:write', 'Asset:read', 'Asset:write'])]
    private ?\DateTimeImmutable $datetime = null;

    #[ORM\Column(length: 255)]
    #[Groups(['AssetAudit:read', 'AssetAudit:write', 'Asset:read', 'Asset:write'])]
    private ?string $actor = null;

    #[ORM\Column(length: 255)]
    #[Groups(['AssetAudit:read', 'AssetAudit:write', 'Asset:read', 'Asset:write'])]
    private ?string $action = null;

    #[ORM\Column(length: 255)]
    #[Groups(['AssetAudit:read', 'AssetAudit:write', 'Asset:read', 'Asset:write', 'AssetAudit:identifier'])]
    private ?string $subject = null;

    #[ORM\ManyToOne(inversedBy: 'assetAudits')]
    #[Groups(['AssetAudit:read', 'AssetAudit:write'])]
    private ?Asset $asset = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['AssetAudit:read', 'AssetAudit:write', 'Asset:read', 'Asset:write'])]
    private array $data = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatetime(): ?\DateTimeImmutable
    {
        return $this->datetime;
    }

    public function setDatetime(\DateTimeImmutable $datetime): self
    {
        $this->datetime = $datetime;

        return $this;
    }

    public function getActor(): ?string
    {
        return $this->actor;
    }

    public function setActor(string $actor): self
    {
        $this->actor = $actor;

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

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

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(?array $data): self
    {
        $this->data = $data;

        return $this;
    }
}
