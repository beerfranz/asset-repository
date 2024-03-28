<?php

namespace App\State;

use App\ApiResource\TaskTemplate as TaskTemplateApi;
use App\Entity\TaskTemplate;


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

    public function __construct(
        EntityManagerInterface $entityManager,
        RequestStack $request,
        LoggerInterface $logger,
        Security $security,
    ) {
        parent::__construct($entityManager, $request, $logger, $security);
        $this->taskTemplateRepo = $entityManager->getRepository(TaskTemplate::class);
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface)
        {
            $taskTemplateEntities = $this->getCollection($this->taskTemplateRepo, $context);

            $output = [];
            foreach($taskTemplateEntities as $taskTemplateEntity) {
                $taskTemplateApi = new TaskTemplateApi();
                $output[] = $taskTemplateApi->populateFromTaskTemplateEntity($taskTemplateEntity);
            }

            return $output;
        }

        $taskTemplateEntity = $this->taskTemplateRepo->findOneByIdentifier($uriVariables['identifier']);
        $taskTemplateApi = new TaskTemplateApi();

        if ($taskTemplateEntity === null)
            return $taskTemplateApi;
        return $taskTemplateApi->populateFromTaskTemplateEntity($taskTemplateEntity);
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

            $taskTemplate = $this->processOneTaskTemplate((array) $data);

            $data->id = $taskTemplate->getId();
        }
    }

    protected function processOneTaskTemplate($data): TaskTemplate
    {
        $taskTemplate = null;

        if (isset($data['identifier'])) {
            $identifier = $data['identifier'];

            $taskTemplate = $this->taskTemplateRepo->findOneByIdentifier($data['identifier']);
        }
        
        if ($taskTemplate === null)
        {
            $taskTemplate = new TaskTemplate();
            $taskTemplate->setIdentifier($data['identifier']);
        }

        if (isset($data['title']))
            $taskTemplate->setTitle($data['title']);

        if (isset($data['description']))
            $taskTemplate->setDescription($data['description']);

        if (isset($data['frequency']))
            $taskTemplate->setFrequency($data['frequency']);

        $this->entityManager->persist($taskTemplate);
        $this->entityManager->flush();
        $this->entityManager->clear();

        return $taskTemplate;
    }

    protected function delete(TaskTemplate $taskTemplate) {
        $this->entityManager->remove($taskTemplate);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }
}
