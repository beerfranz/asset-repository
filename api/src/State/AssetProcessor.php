<?php

namespace App\State;

use App\ApiResource\Asset as AssetDto;

use App\Entity\Asset;
use App\Entity\Owner;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RequestStack;

use Psr\Log\LoggerInterface;

final class AssetProcessor implements ProcessorInterface
{
    protected $logger;
    protected $entityManager;
    protected $request;
    protected $security;

    public function __construct(
        EntityManagerInterface $entityManager,
        RequestStack $request,
        LoggerInterface $logger,
        Security $security
    ) {
        $this->entityManager = $entityManager;
        $this->request = $request->getCurrentRequest();
        $this->logger = $logger;
        $this->security = $security;
    }

    /**
     * @param AssetDto $data
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $repo = $this->entityManager->getRepository(Asset::class);

        $identifiers = [];

        $user = $this->security->getUser();

        $identifier = $data['identifier'];

        $asset = $repo->findOneByIdentifier($identifier);

        if ($asset === null)
        {
            $asset = new Asset();
            $asset->setIdentifier($identifier);
        }
        
        if (isset($data['attributes']))
            $asset->setAttributes($data['attributes']);

        $this->setOwner($asset, $data['owner']);
        
        $identifiers[] = $asset->getIdentifier();

        $this->entityManager->persist($asset);
        $this->entityManager->flush();

    }

}
