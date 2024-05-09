<?php

namespace App\Entity;

use App\Entity\RogerEntity;
use App\Repository\AuditRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuditRepository::class)]
#[Index(name: "subject_kind_idx", columns: ["subjectKind"])]
#[Index(name: "subject_idx", columns: ["subject"])]
#[Index(name: "actor_idx", columns: ["actor"])]
class Audit extends RogerEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $subjectKind = null;

    #[ORM\Column(length: 255)]
    private ?string $subject = null;

    #[ORM\Column(length: 255)]
    private ?string $actor = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $datetime = null;

    #[ORM\Column(length: 255)]
    private ?string $action = null;

    #[ORM\Column]
    private array $data = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubjectKind(): ?string
    {
        return $this->subjectKind;
    }

    public function setSubjectKind(string $subjectKind): static
    {
        $this->subjectKind = $subjectKind;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getActor(): ?string
    {
        return $this->actor;
    }

    public function setActor(string $actor): static
    {
        $this->actor = $actor;

        return $this;
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

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }
}
