<?php

namespace App\State;

use App\ApiResource\RiskManager as RiskManagerDto;
use App\Entity\RiskManager;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\State\ProviderInterface;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RequestStack;

use Psr\Log\LoggerInterface;

final class RiskManagerState extends CommonState implements ProcessorInterface, ProviderInterface
{
    protected $riskManagerRepo;

    public function __construct(
        EntityManagerInterface $entityManager,
        RequestStack $request,
        LoggerInterface $logger,
        Security $security,
    ) {
        parent::__construct($entityManager, $request, $logger, $security);
        $this->riskManagerRepo = $entityManager->getRepository(RiskManager::class);
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface)
        {
            return $this->getCollection($this->riskManagerRepo, $context);
        }

        return $this->riskManagerRepo->findOneByIdentifier($uriVariables['identifier']);
    }
    
    /**
     * @param $data
     * @return T2
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $user = $this->security->getUser();

        if ($operation instanceof Delete) {
            $this->delete($data);
        } else {
            if (isset($uriVariables['id']))
                $data->id = $uriVariables['id'];

            if (isset($uriVariables['identifier']))
                $data->identifier = $uriVariables['identifier'];

            $riskManager = $this->processOneRiskManager((array) $data);

            $data->id = $riskManager->getId();
        }
    }

    protected function processOneRiskManager($data): RiskManager
    {
        if (isset($data['identifier'])) {
            $identifier = $data['identifier'];

            $riskManager = $this->riskManagerRepo->findOneByIdentifier($identifier);
        } else {
            $riskManager = $this->riskManager->find($data['id']);
            if ($riskManager === null)
                throw new NotFoundHttpException('Sorry not existing!');
        }
        
        if ($riskManager === null)
        {
            $riskManager = new RiskManager();
            $riskManager->setIdentifier($identifier);
        }

        if (isset($data['values']))
            $riskManager->setValues($data['values']);

        if (isset($data['valuesAggregator']))
            $riskManager->setValuesAggregator($data['valuesAggregator']);

        if (isset($data['triggers']))
            $riskManager->setTriggers($data['triggers']);

        $this->entityManager->persist($riskManager);
        $this->entityManager->flush();
        $this->entityManager->clear();

        return $riskManager;
    }

    protected function delete(RiskManager $riskManager) {
        $this->entityManager->remove($riskManager);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }
}
