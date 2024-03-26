<?php

namespace App\Entity;

use App\Repository\IndicatorValueRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IndicatorValueRepository::class)]
class IndicatorValue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $datetime = null;

    #[ORM\ManyToOne(inversedBy: 'indicatorValues')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Indicator $indicator = null;

    #[ORM\Column]
    private ?bool $isValidated = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $validator = null;

    #[ORM\Column]
    private ?int $value = null;

    #[ORM\Column(length: 255)]
    private ?string $identifier = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatetime(): ?\DateTimeImmutable
    {
        return $this->datetime;
    }

    public function setDatetime(\DateTimeImmutable $datetime): static
    {
        $this->datetime = $datetime;

        return $this;
    }

    public function getIndicator(): ?Indicator
    {
        return $this->indicator;
    }

    public function setIndicator(?Indicator $indicator): static
    {
        $this->indicator = $indicator;

        return $this;
    }

    public function isIsValidated(): ?bool
    {
        return $this->isValidated;
    }

    public function setIsValidated(bool $isValidated): static
    {
        $this->isValidated = $isValidated;

        return $this;
    }

    public function getValidator(): ?string
    {
        return $this->validator;
    }

    public function setValidator(?string $validator): static
    {
        $this->validator = $validator;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function generateIdentifier(): string
    {
        $frequency = $this->getFrequency();
        $dateTime = $this->getDatetime();

        if ($frequency === 'monthly') {
            return $dateTime->format('Y-m');
        } elseif ($frequency === 'quarterly') {
            return $dateTime->format('Y-m');
        }
    }

    public function identifierIsValid($identifier): bool
    {
        $frequency = $this->getFrequency();

        if ($frequency === 'monthly') {
            // Identifier format must be YYYY-MM
            if (preg_match($identifier, '/^[0-9]{4}-[0-1][0-9]$/') === 1)
                return true;
            else
                return false;
        }
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
}