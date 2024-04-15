<?php

namespace App\State;

use App\ApiResource\RogerApiResourceInterface;
use App\Entity\RogerEntityInterface;
use App\Service\RogerServiceInterface;
use App\State\RogerStateFacade;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\State\Pagination\Pagination;
use ApiPlatform\State\Pagination\TraversablePaginator;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RequestStack;

use Psr\Log\LoggerInterface;

abstract class RogerState implements ProcessorInterface, ProviderInterface, RogerStateInterface
{
  protected $request;
  protected $logger;
  protected $security;
  protected $pagination;
  protected $uriVariables = [];
  protected $context = [];

  public function __construct(
    protected RogerStateFacade $facade,
    protected RogerServiceInterface $service,
  ) {
    $this->request = $facade->getCurrentRequest();
    $this->logger = $facade->getLogger();
    $this->security = $facade->getSecurity();
    $this->pagination = $facade->getPagination();
  }

  public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
  {
    return $this->stateProvide($operation, $uriVariables, $context);
  }

  protected function stateProvide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
  {
    $this->uriVariables = $uriVariables;
    $this->context = $context;

    if ($operation instanceof CollectionOperationInterface)
    {
      $currentPage = $this->pagination->getPage($context);
      $itemsPerPage = $this->pagination->getLimit($operation, $context);
      $offset = $this->pagination->getOffset($operation, $context);
      $entities = $this->getCollection($context);
      $totalItems = count($entities);

      $output = [];
      foreach($entities as $entity) {
        $api = $this->newApi();
        $output[] = $this->fromEntityToApi($entity, $api);
      }

      return new TraversablePaginator(
        new \ArrayIterator($output),
        $currentPage,
        $itemsPerPage,
        $totalItems,
      );
    }

    if (count($uriVariables) === 1 && isset($uriVariables['identifier']))
      $entity = $this->getEntityByIdentifier($uriVariables['identifier']);
    else
      $entity = $this->getEntityByIdentifiers($uriVariables);

    $api = $this->newApi();

    if ($entity === null && $operation instanceof Put)
      return $api;

    if ($entity === null)
      throw new NotFoundHttpException('Not found');

    return $this->fromEntityToApi($entity, $api);
  }

  /**
   * @param $api
   * @return T2
   */
  public function process(mixed $api, Operation $operation, array $uriVariables = [], array $context = [])
  {
    return $this->stateProcess($api, $operation, $uriVariables, $context);
  }

  public function stateProcess(mixed $api, Operation $operation, array $uriVariables = [], array $context = [])
  {
    $this->uriVariables = $uriVariables;
    $this->context = $context;

    if ($operation instanceof Delete) {
      $entity = $this->getEntityByIdentifier($uriVariables['identifier']);

      if ($entity === null)
        throw new NotFoundHttpException('Not found for delete');

      $this->deleteEntity($entity);

    } else {
      if (isset($uriVariables['identifier']))
        $api->__set('identifier', $uriVariables['identifier']);

      $entity = $this->processOneEntity($api);

      return $this->fromEntityToApi($entity, $this->newApi());
    }
  }

  protected function processOneEntity(RogerApiResourceInterface $api): RogerEntityInterface
  {

    $identifier = $api->__get('identifier');

    if ($identifier !== null) {
      $entity = $this->getEntityByIdentifier($identifier);
    }

    if ($entity === null)
      $entity = $this->service->newEntity();
    
    $this->fromApiToEntity($api, $entity);

    $this->persistEntity($entity);

    return $entity;
  }

  protected function simpleFromEntityToApi($entity, $api)
  {
    return $api->fromEntityToApi($entity);
  }


  // Service abstractions

  protected function getEntityByIdentifier($identifier)
  {
    return $this->service->findOneByIdentifier($identifier);
  }

  protected function getEntityByIdentifiers(array $identifiers)
  {
    return $this->service->findOneByIdentifiers($identifiers);
  }

  protected function deleteEntity($entity)
  {
    return $this->service->deleteEntity($entity);
  }

  protected function persistEntity($entity)
  {
    return $this->service->persistEntity($entity);
  }

  protected function getCollection($context)
  {
    return $this->service->getCollection($context);
  }

  // End of service abstractions

 }
