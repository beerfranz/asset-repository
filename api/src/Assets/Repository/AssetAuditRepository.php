<?php

namespace App\Assets\Repository;

use App\Assets\Entity\AssetAudit;

use Beerfranz\RogerBundle\Repository\RogerRepositoryInterface;
use Beerfranz\RogerBundle\Repository\RogerRepositoryTrait;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AssetAudit>
 *
 * @method AssetAudit|null find($id, $lockMode = null, $lockVersion = null)
 * @method AssetAudit|null findOneBy(array $criteria, array $orderBy = null)
 * @method AssetAudit[]    findAll()
 * @method AssetAudit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssetAuditRepository extends ServiceEntityRepository implements RogerRepositoryInterface
{
    use RogerRepositoryTrait;
    
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AssetAudit::class);
    }

    public function save(AssetAudit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AssetAudit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

}
