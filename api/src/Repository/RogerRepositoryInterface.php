<?php

namespace App\Repository;

interface RogerRepositoryInterface
{
    public function rogerFindBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array|Object;

    public function findOneByName($value): ?Object;

    public function findOneByIdentifier($value): ?Object;
}
