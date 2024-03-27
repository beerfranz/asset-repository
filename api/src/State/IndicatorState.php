<?php

namespace App\State;

use App\ApiResource\Indicator as IndicatorApi;
use App\Entity\Indicator;
use App\Entity\IndicatorValue;
use App\Service\FrequencyService;

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

final class IndicatorState extends CommonState implements ProcessorInterface, ProviderInterface
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
        $this->indicatorRepo = $entityManager->getRepository(Indicator::class);
        $this->indicatorValueRepo = $entityManager->getRepository(IndicatorValue::class);
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface)
        {
            $indicatorEntities = $this->getCollection($this->indicatorRepo, $context);

            $output = [];
            foreach($indicatorEntities as $indicatorEntity) {
                $indicatorApi = new IndicatorApi();
                $indicatorApi->populateFromIndicatorEntity($indicatorEntity);

                $valuesSample = $this->indicatorValueRepo->findIndicatorSample($indicatorEntity);
                $indicatorApi->setValuesSample($valuesSample);

                $output[] = $indicatorApi;
            }

            return $output;
        }

        $indicatorEntity = $this->indicatorRepo->findOneByIdentifier($uriVariables['identifier']);
        $indicatorApi = new IndicatorApi();

        if ($indicatorEntity === null)
            return $indicatorApi;
        return $indicatorApi->populateFromIndicatorEntity($indicatorEntity);
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

            $indicator = $this->processOneIndicator((array) $data);

            $data->id = $indicator->getId();
        }
    }

    protected function processOneIndicator($data): Indicator
    {
        $indicator = null;

        if (isset($data['identifier'])) {
            $identifier = $data['identifier'];

            $indicator = $this->indicatorRepo->findOneByIdentifier($data['identifier']);
        }
        
        if ($indicator === null)
        {
            $indicator = new Indicator();
            $indicator->setIdentifier($data['identifier']);
        }

        if (isset($data['namespace']))
            $indicator->setNamespace($data['namespace']);

        if (isset($data['description']))
            $indicator->setDescription($data['description']);

        if (isset($data['isActivated']))
            $indicator->setIsActivated($data['isActivated']);

        if (isset($data['triggers']))
            $indicator->setTriggers($data['triggers']);

        if (isset($data['frequency']))
            $indicator->setFrequency($data['frequency']);

        $this->entityManager->persist($indicator);
        $this->entityManager->flush();
        $this->entityManager->clear();

        return $indicator;
    }

    protected function delete(Indicator $indicator) {
        $this->entityManager->remove($indicator);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }
}
