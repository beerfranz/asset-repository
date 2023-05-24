<?php

namespace App\State;

use App\Entity\Version;
use App\Entity\Owner;
use App\ApiResource\Version as VersionDto;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\State\ProviderInterface;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RequestStack;


use Psr\Log\LoggerInterface;

final class VersionState extends CommonState implements ProcessorInterface, ProviderInterface
{
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $repo = $this->entityManager->getRepository(Version::class);

        if ($operation instanceof CollectionOperationInterface)
        {
            return $this->getCollection($repo, $context);
        }

        return $repo->findOneByAssetDefinitionAndName(null, $uriVariables['name']);
    }
    
    /**
     * @param BatchAssetDto $data
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $repo = $this->entityManager->getRepository(Version::class);

        return false;
    }
}
