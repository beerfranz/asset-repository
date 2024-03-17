<?php

namespace App\State;

use App\ApiResource\Risk as RiskDto;
use App\Entity\Risk;

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

final class RiskState extends CommonState implements ProcessorInterface, ProviderInterface
{
    protected $riskRepo;

    public function __construct(
        EntityManagerInterface $entityManager,
        RequestStack $request,
        LoggerInterface $logger,
        Security $security,
    ) {
        parent::__construct($entityManager, $request, $logger, $security);
        $this->riskRepo = $entityManager->getRepository(Risk::class);
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface)
        {
            return $this->getCollection($this->riskRepo, $context);
        }

        return $this->riskRepo->findOneByIdentifier($uriVariables['identifier']);
    }
    
    /**
     * @param $data
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

            $risk = $this->processOneRisk((array) $data);

            $data->id = $risk->getId();
        }
    }

    protected function processOneRisk($data): Risk
    {

        if (isset($data['identifier'])) {
            $identifier = $data['identifier'];

            $risk = $this->riskRepo->findOneByIdentifier($identifier);
        } else {
            $risk = $this->riskRepo->find($data['id']);
            if ($risk === null)
                throw new NotFoundHttpException('Sorry not existing!');
        }
        
        if ($risk === null)
        {
            $risk = new Risk();
            $risk->setIdentifier($identifier);
        }

        // if (isset($data['values']))
        //     $risk->setValues($values);

        // if (isset($data['valuesAggregator']))
        //     $risk->setValuesAggregator($data['valuesAggregator']);

        // if (isset($data['triggers']))
        //     $risk->setTriggers($data['triggers']);

        $this->entityManager->persist($risk);
        $this->entityManager->flush();
        $this->entityManager->clear();

        return $risk;
    }

    protected function delete(Risk $risk) {
        $this->entityManager->remove($risk);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }
}
