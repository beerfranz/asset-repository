<?php

namespace App\State;

use App\ApiResource\IndicatorValue as IndicatorValueApi;
use App\Entity\Indicator;
use App\Entity\IndicatorValue;
use App\Service\TriggerService;

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

final class IndicatorValueState extends CommonState implements ProcessorInterface, ProviderInterface
{
    protected $indicatorRepo;
    protected $indicatorValueRepo;

    public function __construct(
        EntityManagerInterface $entityManager,
        RequestStack $request,
        LoggerInterface $logger,
        Security $security,
        TriggerService $triggerService,
    ) {
        parent::__construct($entityManager, $request, $logger, $security);
        $this->triggerService = $triggerService;
        $this->indicatorValueRepo = $entityManager->getRepository(IndicatorValue::class);
        $this->indicatorRepo = $entityManager->getRepository(Indicator::class);
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface)
        {
            $indicatorValueEntities = $this->getCollection($this->indicatorValueRepo, $context);

            $output = [];
            foreach($indicatorValueEntities as $indicatorValueEntity) {
                $indicatorValueApi = new IndicatorValueApi();
                $output[] = $indicatorValueApi->fromEntityToApi($indicatorValueEntity);
            }

            return $output;
        }

        $indicatorValueApi = new IndicatorValueApi();

        $indicatorEntity = $this->indicatorRepo->findOneByIdentifier($uriVariables['indicatorIdentifier']);
        if ($indicatorEntity === null) {
            $indicatorValueApi->identifier = $uriVariables['identifier'];
            return $indicatorValueApi;
        }

        $indicatorValueEntity = $this->indicatorValueRepo->findOneByIndicatorAndIdentifier($indicatorEntity, $uriVariables['identifier']);

        if ($indicatorValueEntity === null){
            $indicatorValueApi->identifier = $uriVariables['identifier'];
            return $indicatorValueApi;
        }
        
        return $indicatorValueApi->fromEntityToApi($indicatorValueEntity);
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
            if (isset($uriVariables['identifier']))
                $data->identifier = $uriVariables['identifier'];

            if (isset($uriVariables['indicatorIdentifier'])) {
                $indicatorEntity = $this->indicatorRepo->findOneByIdentifier($uriVariables['indicatorIdentifier']);
                if ($indicatorEntity === null) {
                    throw new \Exception('Indicator not found');
                }
            }
            
            $indicatorValue = $this->processOneIndicatorValue($data, $indicatorEntity);

            $data->id = $indicatorValue->getId();

            $indicatorValueApi = new IndicatorValueApi();
            $indicatorValueApi->fromEntityToApi($indicatorValue);
            return $indicatorValueApi;
        }
    }

    protected function processOneIndicatorValue($data, Indicator $indicator): IndicatorValue
    {
        $indicatorValue = null;

        $identifier = $data->__get('identifier');

        if ($identifier !== null) {
            $indicatorValue = $this->indicatorValueRepo->findOneByIndicatorAndIdentifier($indicator, $identifier);
        }
        
        if ($indicatorValue === null)
        {
            $indicatorValue = new IndicatorValue();
            $indicatorValue->setIdentifier($identifier);
            $indicatorValue->setIndicator($indicator);
            $indicatorValue->setDatetime(new \DateTimeImmutable());
            $indicatorValue->setIsValidated(false);
        }

        $indicatorValue->setIndicator($indicator);
        $indicatorValue->setIsValidated($data->isValidated || false);

        $trigger = $this->triggerService->calculateTrigger($indicator->getTriggers(), $indicatorValue->getValue());
        $indicatorValue->setTrigger($trigger->toArray());

        $this->entityManager->persist($indicatorValue);
        $this->entityManager->flush();
        $this->entityManager->clear();

        return $indicatorValue;
    }

    protected function delete(IndicatorValue $indicatorValue) {
        $this->entityManager->remove($indicatorValue);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }
}
