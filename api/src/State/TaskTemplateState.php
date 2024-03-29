<?php

namespace App\State;

use App\ApiResource\TaskTemplate as TaskTemplateApi;
use App\Entity\TaskTemplate;

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

final class TaskTemplateState extends CommonState implements ProcessorInterface, ProviderInterface
{
    protected $taskTemplateRepo;
    protected $frequencyService;

    public function __construct(
        EntityManagerInterface $entityManager,
        RequestStack $request,
        LoggerInterface $logger,
        Security $security,
        FrequencyService $frequencyService,
    ) {
        parent::__construct($entityManager, $request, $logger, $security);
        $this->taskTemplateRepo = $entityManager->getRepository(TaskTemplate::class);
        $this->frequencyService = $frequencyService;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface)
        {
            $taskTemplateEntities = $this->getCollection($this->taskTemplateRepo, $context);

            $output = [];
            foreach($taskTemplateEntities as $taskTemplateEntity) {
                $taskTemplateApi = new TaskTemplateApi();
                $output[] = $taskTemplateApi->fromEntityToApi($taskTemplateEntity);
            }

            return $output;
        }

        $taskTemplateEntity = $this->taskTemplateRepo->findOneByIdentifier($uriVariables['identifier']);
        $taskTemplateApi = new TaskTemplateApi();

        if ($taskTemplateEntity === null)
            return $taskTemplateApi;

        $taskTemplateApi->fromEntityToApi($taskTemplateEntity);

        return $taskTemplateApi->fromEntityToApi($taskTemplateEntity);
    }
    
    /**
     * @param $data
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $user = $this->security->getUser();

        if ($operation instanceof Delete) {
            $taskTemplate = $this->getEntity($uriVariables['identifier']);
            $this->deleteEntity($taskTemplate);
        } else {
            if (isset($uriVariables['identifier']))
                $data->identifier = $uriVariables['identifier'];

            $taskTemplate = $this->processOneTaskTemplate($data);

            $data->id = $taskTemplate->getId();
        }
    }

    protected function getEntity($identifier)
    {
        return $this->taskTemplateRepo->findOneByIdentifier($identifier);
    }

    protected function processOneTaskTemplate($data): TaskTemplate
    {
        $taskTemplate = null;

        $identifier = $data->__get('identifier');

        if ($identifier !== null) {
            $taskTemplate = $this->getEntity($identifier);
        }
        
        if ($taskTemplate === null)
        {
            $taskTemplate = new TaskTemplate();
        }

        $taskTemplate = $data->fromApiToEntity($taskTemplate);

        $taskTemplate = $this->frequencyService->calculateNextIteration($taskTemplate);

        $this->entityManager->persist($taskTemplate);
        $this->entityManager->flush();
        $this->entityManager->clear();

        return $taskTemplate;
    }
}
