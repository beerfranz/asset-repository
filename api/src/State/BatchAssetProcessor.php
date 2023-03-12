<?php

namespace App\State;

use App\Entity\Asset;
use App\ApiResource\BatchAsset;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RequestStack;

use Psr\Log\LoggerInterface;

final class BatchAssetProcessor implements ProcessorInterface
{
    protected $logger;
    protected $entityManager;
    protected $request;

    public function __construct(
        EntityManagerInterface $entityManager,
        RequestStack $request,
        LoggerInterface $logger
    ) {
        $this->entityManager = $entityManager;
        $this->request = $request->getCurrentRequest();
        $this->logger = $logger;
    }

    // public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ?object
    // {

    //     $repo = $this->_entityManager->getRepository(OperationEntity::class);
    // }

    /**
     * @param BatchAssetDto $data
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        foreach($data->assets as $obj)
        {

            $identifier = $obj['identifier'];
            
            $asset = new Asset();
            $asset->setIdentifier($identifier);

            // $repo = $this->_entityManager->getRepository(Asset::class);

            $this->entityManager->persist($asset);
            $this->entityManager->flush();
        }

    }
}
