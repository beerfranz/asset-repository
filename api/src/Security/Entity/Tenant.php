<?php

namespace App\Security\Entity;

use App\Security\Repository\TenantRepository;

use Beerfranz\RogerBundle\Entity\RogerEntity;
use Beerfranz\RogerBundle\Entity\RogerIdentifierTrait;
use Beerfranz\RogerBundle\Entity\RogerEnabledTrait;
use Beerfranz\RogerBundle\Entity\RogerAttributesTrait;
use Beerfranz\RogerBundle\Doctrine\RogerListener;

use ApiPlatform\Metadata\ApiResource;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TenantRepository::class)]
#[ORM\EntityListeners([RogerListener::class])]
#[ApiResource(
    security: "is_granted('ROLE_SUPER_ADMIN')",
    routePrefix: '/admin',
)]
class Tenant extends RogerEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['Roger:Messenger'])]
    private ?int $id = null;

    use RogerIdentifierTrait;

    use RogerEnabledTrait;

    use RogerAttributesTrait;

    public function getId(): ?int
    {
    	return $this->id;
    }
}
