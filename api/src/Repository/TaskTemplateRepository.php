<?php

namespace App\Repository;

use App\Entity\TaskTemplate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * @extends ServiceEntityRepository<TaskTemplate>
 *
 * @method TaskTemplate|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskTemplate|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskTemplate[]    findAll()
 * @method TaskTemplate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskTemplateRepository extends ServiceEntityRepository implements RogerRepositoryInterface
{
    use RogerRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskTemplate::class);
    }

    public function getFrequencyToUpdate(): array
    {
        $qb = $this->createQueryBuilder('t');

        return $qb
                    ->where($qb->expr()->isNotNull("t.frequency"))
                    ->getQuery()
                    ->getResult();
    }

    public function getTaskToGenerate(): array
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $result = $this->getEntityManager()->createNativeQuery("SELECT id FROM task_template WHERE cast(frequency->'nextIterationAt'->>'date' as timestamp) < now()", $rsm)
           ->getResult()
        ;
        return $result;
    }

    public function findChildren(TaskTemplate $taskTemplate): array
    {
        return $this->createQueryBuilder('t')
           ->andWhere('t.parent = :val')
           ->setParameter('val', $taskTemplate)
           ->getQuery()
           ->getResult()
        ;
    }

//    /**
//     * @return TaskTemplate[] Returns an array of TaskTemplate objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TaskTemplate
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
