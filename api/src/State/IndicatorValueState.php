<?php

namespace App\State;

use App\ApiResource\IndicatorValue as IndicatorValueApi;
use App\Entity\Indicator;
use App\Entity\IndicatorValue;


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
    ) {
        parent::__construct($entityManager, $request, $logger, $security);
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
                $output[] = $indicatorValueApi->populateFromIndicatorValueEntity($indicatorValueEntity);
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

        return $indicatorValueApi->populateFromIndicatorValueEntity($indicatorValueEntity);
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
            if (isset($uriVariables['identifier']))
                $data->identifier = $uriVariables['identifier'];

            if (isset($uriVariables['indicatorIdentifier'])) {
                $indicatorEntity = $this->indicatorRepo->findOneByIdentifier($uriVariables['indicatorIdentifier']);
                if ($indicatorEntity === null) {
                    throw new \Exception('Indicator not found');
                }
            }
            
            $indicatorValue = $this->processOneIndicatorValue((array) $data, $indicatorEntity);

            $data->id = $indicatorValue->getId();

            $indicatorValueApi = new IndicatorValueApi();
            $indicatorValueApi->populateFromIndicatorValueEntity($indicatorValue);
            return $indicatorValueApi;
        }
    }

    protected function processOneIndicatorValue($data, Indicator $indicator): IndicatorValue
    {
        $indicatorValue = null;

        if (isset($data['identifier'])) {
            $identifier = $data['identifier'];

            $indicatorValue = $this->indicatorValueRepo->findOneByIndicatorAndIdentifier($indicator, $data['identifier']);
        }
        
        if ($indicatorValue === null)
        {
            $indicatorValue = new IndicatorValue();
            $indicatorValue->setIdentifier($identifier);
            $indicatorValue->setIndicator($indicator);
            $indicatorValue->setDatetime(new \DateTimeImmutable());
            $indicatorValue->setIsValidated(false);
        }

        if (isset($data['value']))
            $indicatorValue->setValue($data['value']);

        if (isset($date['isValidated']))
            $indicatorValue->setIsValidated($data['isValidated']);

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
