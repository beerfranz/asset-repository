<?php

namespace App\State;

use App\ApiResource\IndicatorValue as IndicatorValueApi;
use App\Entity\Indicator;
use App\Entity\IndicatorValue as IndicatorValueEntity;
use App\Service\IndicatorValueService;
use App\Service\TriggerService;

use App\State\RogerStateFacade;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class IndicatorValueState extends RogerState
{

    public function __construct(
        RogerStateFacade $facade,
        IndicatorValueService $service,
        protected TriggerService $triggerService,
    ) {
        parent::__construct($facade, $service);
    }

    public function newApi(): IndicatorValueApi
    {
        return new IndicatorValueApi();
    }

    public function fromApiToEntity($api, $entity): IndicatorValueEntity
    {
        $indicator = $this->service->findOneIndicatorByIdentifier($this->uriVariables['indicatorIdentifier']);

        if ($entity->getIdentifier() === null){
            $entity->setIdentifier($api->__get('identifier'));
            $entity->setIndicator($indicator);
            $entity->setDatetime(new \DateTimeImmutable());
            $entity->setIsValidated(false);
        }

        $entity->setValue($api->__get('value'));
        $entity->setIsValidated($api->isValidated || false);

        $trigger = $this->triggerService->calculateTrigger($indicator->getTriggers(), $entity->getValue());
        $entity->setTrigger($trigger->toArray());

        return $entity;
    }

    public function fromEntityToApi($entity, $api): IndicatorValueApi
    {   
        return $api->fromEntityToApi($entity);
    }
    
    // /**
    //  * @param $data
    //  * @return T2
    //  */
    // public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    // {
    //     $user = $this->security->getUser();

    //     if ($operation instanceof Delete) {
    //         $this->delete($data);
    //     } else {
    //         if (isset($uriVariables['identifier']))
    //             $data->identifier = $uriVariables['identifier'];

    //         if (isset($uriVariables['indicatorIdentifier'])) {
    //             $indicatorEntity = $this->indicatorRepo->findOneByIdentifier($uriVariables['indicatorIdentifier']);
    //             if ($indicatorEntity === null) {
    //                 throw new \Exception('Indicator not found');
    //             }
    //         }
            
    //         $indicatorValue = $this->processOneIndicatorValue($data, $indicatorEntity);

    //         $data->id = $indicatorValue->getId();

    //         $indicatorValueApi = new IndicatorValueApi();
    //         $indicatorValueApi->fromEntityToApi($indicatorValue);
    //         return $indicatorValueApi;
    //     }
    // }

    // protected function processOneIndicatorValue($data, Indicator $indicator): IndicatorValue
    // {
    //     $indicatorValue = null;

    //     $identifier = $data->__get('identifier');

    //     if ($identifier !== null) {
    //         $indicatorValue = $this->indicatorValueRepo->findOneByIndicatorAndIdentifier($indicator, $identifier);
    //     }
        
    //     if ($indicatorValue === null)
    //     {
    //         $indicatorValue = new IndicatorValue();
    //         $indicatorValue->setIdentifier($identifier);
    //         $indicatorValue->setIndicator($indicator);
    //         $indicatorValue->setDatetime(new \DateTimeImmutable());
    //         $indicatorValue->setIsValidated(false);
    //     }

    //     $indicatorValue->setIndicator($indicator);
    //     $indicatorValue->setIsValidated($data->isValidated || false);

    //     $trigger = $this->triggerService->calculateTrigger($indicator->getTriggers(), $indicatorValue->getValue());
    //     $indicatorValue->setTrigger($trigger->toArray());

    //     $this->entityManager->persist($indicatorValue);
    //     $this->entityManager->flush();
    //     $this->entityManager->clear();

    //     return $indicatorValue;
    // }

}
