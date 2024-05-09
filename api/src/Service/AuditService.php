<?php

namespace App\Service;

use App\Entity\Audit;

use Doctrine\ORM\EntityManagerInterface;

use Psr\Log\LoggerInterface;

class AuditService extends RogerService
{

  protected $taskWorkflowRepo;

  public function __construct(
    EntityManagerInterface $entityManager,
    LoggerInterface $logger,
  ) {
    parent::__construct($entityManager, $logger, Audit::class);
  }

  public function newEntity(): Audit
  {
    $entity = new Audit();

    return $entity;
  }

  public function find($kind, $identifier): array
  {
    return $this->repo->findBy(
      [
        'subjectKind' => $kind,
        'subject' => $identifier,
      ], [
        'datetime' => 'DESC',
      ]);
  }

}
