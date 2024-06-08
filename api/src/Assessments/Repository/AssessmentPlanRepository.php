<?php

namespace App\Assessments\Repository;

use App\Assessments\Entity\AssessmentPlan;

use Beerfranz\RogerBundle\Repository\RogerRepositoryInterface;
use Beerfranz\RogerBundle\Repository\RogerRepositoryTrait;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AssessmentPlan>
 *
 * @method AssessmentPlan|null find($id, $lockMode = null, $lockVersion = null)
 * @method AssessmentPlan|null findOneBy(array $criteria, array $orderBy = null)
 * @method AssessmentPlan[]    findAll()
 * @method AssessmentPlan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssessmentPlanRepository extends ServiceEntityRepository implements RogerRepositoryInterface
{
	use RogerRepositoryTrait;
	
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, AssessmentPlan::class);
	}

}
