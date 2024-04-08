<?php

namespace App\State;

use App\ApiResource\RogerApiResourceInterface;
use App\Entity\RogerEntityInterface;
use App\Service\RogerServiceInterface;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\State\ProviderInterface;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RequestStack;

use Psr\Log\LoggerInterface;

abstract class RogerState implements ProcessorInterface, ProviderInterface, RogerStateInterface
{
  protected $service;
  protected $logger;
  protected $request;
  protected $security;

  public function __construct(
    RequestStack $request,
    LoggerInterface $logger,
    Security $security,
    RogerServiceInterface $service,
  ) {
    $this->request = $request->getCurrentRequest();
    $this->logger = $logger;
    $this->security = $security;
    $this->service = $service;
  }

  public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
  {
    return $this->stateProvide($operation, $uriVariables, $context);
  }

  protected function stateProvide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
  {
    if ($operation instanceof CollectionOperationInterface)
    {
      $entities = $this->getCollection($context);

      $output = [];
      foreach($entities as $entity) {
        $api = $this->newApi();
        $output[] = $this->fromEntityToApi($entity, $api);
      }

      return $output;
    }

    $entity = $this->getEntityByIdentifier($uriVariables['identifier']);
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
