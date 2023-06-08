<?php

namespace App\State;

use App\Entity\Environment;

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

final class EnvironmentState extends CommonState implements ProcessorInterface, ProviderInterface
{
    protected $environmentRepo;

    public function __construct(
        EntityManagerInterface $entityManager,
        RequestStack $request,
        LoggerInterface $logger,
        Security $security,
    ) {
        parent::__construct($entityManager, $request, $logger, $security);
        $this->environmentRepo = $entityManager->getRepository(Environment::class);
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface)
        {
            return $this->getCollection($this->environmentRepo, $context);
        }

        return $this->environmentRepo->findOneByIdentifier(null, $uriVariables['identifier']);
    }
    
    /**
     * @param $data
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {

        $user = $this->security->getUser();

        $this->processOneEnvironment($data);
    }

    protected function processOneEnvironment($data): Asset
    {
        $identifier = $data['identifier'];

        $environment = $this->environmentRepo->findOneByIdentifier($identifier);

        if ($environment === null)
        {
            $environment = new Environment();
            $environment->setIdentifier($identifier);
        }

        if (isset($data['name']))
            $environment->setName($data['name']);
        else
            $environment->setName(ucfirst($identifier));

        $this->entityManager->persist($environment);
        $this->entityManager->flush();

        return $environment;
    }
}
