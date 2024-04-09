<?php

namespace App\State;

use App\ApiResource\Risk as RiskApi;
use App\Entity\Risk as RiskEntity;

use App\Service\RiskService;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\State\ProviderInterface;

use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RequestStack;

use Psr\Log\LoggerInterface;

final class RiskState extends RogerState
{

    public function __construct(
        RequestStack $request,
        LoggerInterface $logger,
        Security $security,
        RiskService $service,
    ) {
        parent::__construct($request, $logger, $security, $service);
    }

    public function newApi(): RiskApi
    {
        return new RiskApi();
    }

    public function fromApiToEntity($api, $entity): RiskEntity
    {
        if ($entity->getIdentifier() === null) {
            $entity->setIdentifier($api->__get('identifier'));
        }

        // $entity = $api->fromApiToEntity($entity);
        $entity->setDescription($api->__get('description'));

        if ($api->__get('asset') !== null) {
            if (isset($api->__get('asset')['identifier'])) {
                $asset = $this->service->findOneAssetByIdentifier($api->__get('asset')['identifier']);
                if ($asset !== null) {
                    $entity->setAsset($asset);
                }
            }
        }

        if (null !== $api->__get('riskManager')) {
            if (null !== $api->__get('riskManager')['identifier']) {
                $riskManager = $this->service->findOneRiskManagerByIdentifier($api->__get('riskManager')['identifier']);
                if ($riskManager !== null) {
                    $entity->setRiskManager($riskManager);
                }
            }
        }

        $values = $api->__get('values');

        $aggregatedRisk = $this->service->aggregateRisk(
            $entity->getRiskManager()->getValuesAggregator(),
            $values,
            $entity->getRiskManager()->getTriggers(),
        );

        $values['aggregatedRisk'] = $aggregatedRisk;
        $entity->setValues($values);

        $values = $entity->getValues();
        $mitigations = $api->__get('mitigations');
        foreach($mitigations as $mitigationId => $mitigation) {
            $values = array_merge($values, $mitigation['effects']);
            $aggregatedRisk = $this->service->aggregateRisk(
                $entity->getRiskManager()->getValuesAggregator(),
                $values,
                $entity->getRiskManager()->getTriggers(),
            );
            $mitigations[$mitigationId]['aggregatedRisk'] = $aggregatedRisk;
        }
        $entity->setMitigations($mitigations);

        return $entity;
    }

    public function fromEntityToApi($entity, $api): RiskApi
    {
        // $this->simpleFromEntityToApi($entity, $api);
        $api->identifier = $entity->getIdentifier();
        $api->asset = $entity->getAsset()->getIdentifier();
        $api->riskManager = $entity->getRiskManager()->getIdentifier();
        $api->description = $entity->getDescription();
        $api->values = $entity->getValues();
        $api->mitigations = $entity->getMitigations();

        return $api;
    }

}
