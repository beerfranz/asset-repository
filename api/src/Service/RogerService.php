<?php

namespace App\Service;

use App\Entity\RogerEntityInterface;
use App\Repository\RogerRepositoryInterface;

use Doctrine\ORM\EntityManagerInterface;

use Psr\Log\LoggerInterface;

abstract class RogerService implements RogerServiceInterface
{
  protected $logger;
  protected $entityManager;
  protected $repo;

  public function __construct(
    EntityManagerInterface $entityManager,
    LoggerInterface $logger,
    string $entityClass,
  ) {
    $this->entityManager = $entityManager;
    $this->logger = $logger;
    $this->repo = $entityManager->getRepository($entityClass);
  }

  public function findOneByIdentifier($identifier): null|RogerEntityInterface
  {
    return $this->repo->findOneByIdentifier($identifier);
  }

  public function findOneByIdentifiers(array $identifiers): null|RogerEntityInterface
  {
    return $this->repo->findOneByIdentifiers($identifiers);
  }

  public function findOneByIdentifierInRepo($identifier, $repo): null|object
  {
    return $repo->findOneByIdentifier($identifier);
  }

  public function deleteEntity(RogerEntityInterface $entity)
  {
    return $this->deleteOneEntity($entity);
  }

  protected function deleteOneEntity(RogerEntityInterface $entity)
  {
    $this->entityManager->remove($entity);
    $this->entityManager->flush();
  }

  public function persistEntity(RogerEntityInterface $entity)
  {
    $this->entityManager->persist($entity);
    $this->entityManager->flush();
  }

  public function getCollection($context)
  {
    // Default values
    $page = 1;
    $itemsPerPage = 10;
    $order = [ 'id' => 'asc'];
    $criteria = [];

    if (isset($context['filters'])) {

      foreach ($context['filters'] as $filter => $value)
      {
        switch($filter) {
          case '_': break;
          case 'page':
          case 'itemsPerPage':
          case 'order':
            $$filter = $value;
            break;
          default:
            $criteria[$filter] = $value;
        }
      }
    }

    if ($this->repo instanceof RogerRepositoryInterface) {
      return $this->repo->rogerFindBy($criteria, $order, $itemsPerPage, ($page - 1) * $itemsPerPage, true);
    }

    return $this->repo->findBy($criteria, $order, $itemsPerPage, ($page - 1) * $itemsPerPage);

  }

}
