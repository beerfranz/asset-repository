<?php

namespace App\Security\Entity;

use App\Security\Entity\User;
use App\Security\Repository\UserGroupRepository;

use ApiPlatform\Metadata\ApiResource;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Serializer\Annotation\Groups;

use Beerfranz\RogerBundle\Doctrine\RogerListener;
use Beerfranz\RogerBundle\Entity\RogerEntity;

#[ORM\Entity(repositoryClass: UserGroupRepository::class)]
#[ORM\EntityListeners([RogerListener::class])]
#[ApiResource(
	security: "is_granted('ROLE_ADMIN')",
)]
class UserGroup extends RogerEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

	#[ORM\Column(length: 255, unique: true, nullable: false)]
	private ?string $identifier = null;

	public function getIdentifier(): ?string
	{
		return $this->identifier;
	}

	public function setIdentifier(string $identifier): self
	{
		$this->identifier = $identifier;

		return $this;
	}

	#[ORM\ManyToMany(targetEntity: AuthorizationPolicy::class, mappedBy: 'groups')]
	private Collection $authorizationPolicies;

	/**
	 * @var Collection<int, User>
	 */
	#[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'groups')]
	private Collection $users;

	public function __construct()
	{
		$this->users = new ArrayCollection();
		$this->authorizationPolicies = new ArrayCollection();
	}

	/**
	 * @return Collection<int, User>
	 */
	public function getUsers(): Collection
	{
		return $this->users;
	}

	public function addUser(User $user): static
	{
		if (!$this->users->contains($user)) {
			$this->users->add($user);
		}

		return $this;
	}

	public function removeUser(User $user): static
	{
		$this->users->removeElement($user);

		return $this;
	}

	public function getUserCount(): int
	{
		return count($this->users);
	}
	
	public function getAuthorizationPolicies(): Collection
	{
		return $this->authorizationPolicies;
	}

	public function addAuthorizationPolicy(AuthorizationPolicy $authorizationPolicy): static
	{
		if (!$this->authorizationPolicies->contains($authorizationPolicy)) {
			$this->authorizationPolicies->add($authorizationPolicy);
		}

		return $this;
	}

	public function removeAuthorizationPolicy(AuthorizationPolicy $authorizationPolicy): static
	{
		$this->authorizationPolicies->removeElement($authorizationPolicy);

		return $this;
	}
}