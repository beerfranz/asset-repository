<?php

namespace App\Security\Entity;

use App\Security\Entity\User;
use App\Security\Repository\AuthorizationRepository;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;

use Beerfranz\RogerBundle\Entity\RogerEntity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuthorizationRepository::class)]
#[ORM\Table(name: '`authorization`')]
#[ORM\Index(name: "authorization_namespace_idx", fields: ["namespace"])]
#[ORM\Index(name: "authorization_object_idx", fields: ["object"])]
#[ApiResource(
	security: "is_granted('ROLE_ADMIN')",
)]
#[GetCollection(
    security: "is_granted('ROLE_ADMIN')",
)]
class Authorization extends RogerEntity	
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column(length: 255)]
	private ?string $namespace = null;

	#[ORM\Column(length: 255)]
	private ?string $object = null;

	#[ORM\Column(length: 255)]
	private ?string $relation = null;

	#[ORM\ManyToOne(inversedBy: 'authorizations')]
	private ?User $user = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $context = null;

	#[ORM\Column]
	private ?int $refreshId = null;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getNamespace(): ?string
	{
		return $this->namespace;
	}

	public function setNamespace(string $namespace): self
	{
		$this->namespace = $namespace;

		return $this;
	}

	public function getObject(): ?string
	{
		return $this->object;
	}

	public function setObject(string $object): self
	{
		$this->object = $object;

		return $this;
	}

	public function getRelation(): ?string
	{
		return $this->relation;
	}

	public function setRelation(string $relation): self
	{
		$this->relation = $relation;

		return $this;
	}

	public function getUser(): ?User
	{
		return $this->user;
	}

	public function setUser(?User $user): static
	{
		$this->user = $user;

		return $this;
	}

	public function getContext(): ?string
	{
		return $this->context;
	}

	public function setContext(string $context): self
	{
		$this->context = $context;

		return $this;
	}

	public function getRefreshId(): ?int
	{
		return $this->refreshId;
	}

	public function setRefreshId(int $refreshId): self
	{
		$this->refreshId = $refreshId;

		return $this;
	}

}