<?php

namespace App\State;

use App\ApiResource\Indicator as IndicatorApi;
use App\Entity\Indicator;
use App\Entity\IndicatorValue;

use App\Service\IndicatorService;
use App\Service\FrequencyService;

use App\State\RogerStateFacade;

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

final class IndicatorState extends RogerState
{

    public function __construct(
        RogerStateFacade $facade,
        IndicatorService $service,
        protected FrequencyService $frequencyService,
    ) {
        parent::__construct($facade, $service);
    }

    public function fromApiToEntity($api, $entity): Indicator
    {
        if ($entity->getIdentifier() === null)
            $entity->setIdentifier($api->__get('identifier'));

        $taskTemplate = null;
        if (null !== $api->__get('taskTemplate') && null !== $api->__get('taskTemplate')->__get('identifier')) {
            $taskTemplate = $this->service->findOneTaskTemplateByIdentifier($api->__get('taskTemplate')->__get('identifier'));
            $api->__set('taskTemplate', null);
        }

        $entity = $api->fromApiToEntity($entity);
        if ($taskTemplate !== null)
            $entity->setTaskTemplate($taskTemplate);

        $this->frequencyService->calculateNextIteration($entity);
        
        return $entity;
    }

    public function fromEntityToApi($entity, $api): IndicatorApi
    {
        $api->identifier = $entity->getIdentifier();
        $api->frequency = $entity->getFrequency();
        $api->namespace = $entity->getNamespace();
        $api->description = $entity->getDescription();
        $api->targetValue = $entity->getTargetValue();
        $api->triggers = $entity->getTriggers();
        $valuesSample = $this->service->findIndicatorSample($entity);
        $api->setValuesSample($valuesSample);

        return $api;
    }

    public function newApi(): IndicatorApi
    {
        return new IndicatorApi();
    }
}
