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
        FrequencyService $frequencyService,
    ) {
        parent::__construct($entityManager, $request, $logger, $security);
        $this->frequencyService = $frequencyService;
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
                $valuesSample = $this->indicatorValueRepo->findIndicatorSample($indicatorEntity);
                $indicatorApi->setValuesSample($valuesSample);
                
                $output[] = $indicatorApi->fromEntityToApi($indicatorEntity);
            }

            return $output;
        }

        $indicatorEntity = $this->indicatorRepo->findOneByIdentifier($uriVariables['identifier']);
        $indicatorApi = new IndicatorApi();

        if ($indicatorEntity === null && $operation instanceof Put)
            return $indicatorApi;

        if ($indicatorEntity === null)
          throw new NotFoundHttpException('Not found');

        $indicatorApi->fromEntityToApi($indicatorEntity);

        return $indicatorApi->fromEntityToApi($indicatorEntity);
    }
    
    /**
     * @param $data
     * @return T2
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $user = $this->security->getUser();

        if ($operation instanceof Delete) {
            $indicatorEntity = $this->indicatorRepo->findOneByIdentifier($uriVariables['identifier']);
            $this->delete($indicatorEntity);
        } else {
            if (isset($uriVariables['identifier']))
                $data->identifier = $uriVariables['identifier'];

            $indicator = $this->processOneIndicator($data);

            $data->id = $indicator->getId();
        }
    }

    protected function processOneIndicator($data): Indicator
    {
        $indicator = null;

        $identifier = $data->__get('identifier');

        if ($identifier !== null) {
            $indicator = $this->getEntity($identifier);
        }
        
        if ($indicator === null)
        {
            $indicator = new Indicator();
        }

        $indicator = $data->fromApiToEntity($indicator);

        $indicator = $this->frequencyService->calculateNextIteration($indicator);

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

    protected function getEntity($identifier)
    {
        return $this->indicatorRepo->findOneByIdentifier($identifier);
    }
}
