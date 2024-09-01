<?php

namespace App\Security\Entity;

Use App\Security\Entity\Membership;
Use App\Security\Entity\User;

use App\Security\Repository\MembershipRepository;

use Beerfranz\RogerBundle\Entity\RogerEntity;
use Beerfranz\RogerBundle\Doctrine\RogerListener;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Security\Core\Membership\MembershipInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

use ApiPlatform\Metadata\ApiResource;

#[ORM\Entity(repositoryClass: MembershipRepository::class)]
#[ApiResource(
    security: "is_granted('ROLE_ADMIN')",
    routePrefix: '/admin',
)]
#[ORM\EntityListeners([RogerListener::class])]
class Membership extends RogerEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['Roger:Messenger'])]
    private ?int $id = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $user = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToOne(targetEntity: Organization::class)]
    private ?Organization $organization = null;

    public const ROLES = [
        'ROLE_ADMIN',
        'ROLE_USER',
    ];

    #[ORM\Column]
    #[Assert\Choice(choices: Membership::ROLES, multiple: true)]
    private array $roles = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function setOrganization(Organization $organization): self
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * @see MembershipInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

}
