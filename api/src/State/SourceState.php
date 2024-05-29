<?php

namespace App\State;

use App\Entity\Source;
use App\Assets\ApiResource\Source as SourceDto;

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

final class SourceState extends CommonState implements ProviderInterface
{

    // public function __construct(private ProviderInterface $itemProvider)
    // {
    // }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // var_dump($context);exit;
        $repo = $this->entityManager->getRepository(Source::class);

        if ($operation instanceof CollectionOperationInterface)
        {
            return $this->getCollection($repo, $context);
        }

        return $repo->findOneByName(null, $uriVariables['name']);
    }
}
