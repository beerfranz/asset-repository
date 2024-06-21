<?php

namespace App\Security\Entity;

use App\Security\Repository\AuthorizationPolicyRepository;

use ApiPlatform\Metadata\ApiResource;

use Beerfranz\RogerBundle\Entity\RogerEntity;
use Beerfranz\RogerBundle\Doctrine\RogerListener;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AuthorizationPolicyRepository::class)]
#[ORM\Index(name: "authorization_policy_namespace_idx", fields: ["namespace"])]
#[ORM\Index(name: "authorization_policy_object_idx", fields: ["object"])]
#[ApiResource(
	security: "is_granted('ROLE_ADMIN')",
)]
#[ORM\EntityListeners([RogerListener::class])]
class AuthorizationPolicy extends RogerEntity
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	#[Groups(['Roger:Messenger'])]
	private ?int $id = null;

	#[ORM\Column(length: 255, unique: true)]
	private ?string $identifier = null;

	#[ORM\Column(length: 255)]
	private ?string $namespace = null;

	#[ORM\Column(length: 255)]
	private ?string $object = null;

	#[ORM\Column(length: 255)]
	private ?string $relation = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $context = null;

	#[ORM\ManyToMany(targetEntity: UserGroup::class, inversedBy: 'authorizationPolicies')]
	private Collection $groups;

	public function __construct()
	{
		$this->groups = new ArrayCollection();
	}

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

	public function getContext(): ?string
	{
		return $this->context;
	}

	public function setContext(string $context): self
	{
		$this->context = $context;

		return $this;
	}

	/**
	 * @return Collection<int, UserGroup>
	 */
	public function getGroups(): Collection
	{
		return $this->groups;
	}

	public function addGroup(UserGroup $group): static
	{
		if (!$this->groups->contains($group)) {
			$this->groups->add($group);
		}

		return $this;
	}

	public function removeGroup(UserGroup $group): static
	{
		$this->groups->removeElement($group);

		return $this;
	}

	public function getGroupCount(): int
	{
		return count($this->groups);
	}

}
