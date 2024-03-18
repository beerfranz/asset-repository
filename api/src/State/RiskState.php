<?php

namespace App\State;

use App\ApiResource\Risk as RiskDto;
use App\Entity\Risk;
use App\Entity\RiskManager;
use App\Entity\Asset;

use App\Service\RiskService;

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
    protected $assetRepo;
    protected $riskManagerRepo;
    protected $riskService;

    public function __construct(
        EntityManagerInterface $entityManager,
        RequestStack $request,
        LoggerInterface $logger,
        Security $security,
        RiskService $riskService,
    ) {
        parent::__construct($entityManager, $request, $logger, $security);
        $this->riskRepo = $entityManager->getRepository(Risk::class);
        $this->riskManagerRepo = $entityManager->getRepository(RiskManager::class);
        $this->assetRepo = $entityManager->getRepository(Asset::class);
        $this->riskService = $riskService;
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

        if (isset($data['asset'])) {
            if (isset($data['asset']['identifier'])) {
                $asset = $this->assetRepo->findOneByIdentifier($data['asset']['identifier']);
                if ($asset !== null) {
                    $risk->setAsset($asset);
                }
            }
        }

        if (isset($data['riskManager'])) {
            if (isset($data['riskManager']['identifier'])) {
                $riskManager = $this->riskManagerRepo->findOneByIdentifier($data['riskManager']['identifier']);
                if ($riskManager !== null) {
                    $risk->setRiskManager($riskManager);
                }
            }
        }

        if (isset($data['description']))
            $risk->setDescription($data['description']);

        if (isset($data['values'])) {
            // Update values aggregator
            $aggregatedRisk = $this->riskService->aggregateRisk(
                $risk->getRiskManager()->getValuesAggregator(),
                $data['values'],
                $risk->getRiskManager()->getTriggers()
            );

            $data['values']['aggregatedRisk'] = $aggregatedRisk;

            $risk->setValues($data['values']);
        }

        if (isset($data['mitigations'])) {
            $values = $risk->getValues();

            // Update mitigations aggregators
            foreach($data['mitigations'] as $mitigationId => $mitigation) {
                $values = array_merge($values, $mitigation['effects']);
                $aggregatedRisk = $this->riskService->aggregateRisk(
                    $risk->getRiskManager()->getValuesAggregator(),
                    $values,
                    $risk->getRiskManager()->getTriggers()
                );
                $data['mitigations'][$mitigationId]['aggregatedRisk'] = $aggregatedRisk;
            }

            $risk->setMitigations($data['mitigations']);
        }

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
