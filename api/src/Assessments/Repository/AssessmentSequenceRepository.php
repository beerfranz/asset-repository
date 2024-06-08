<?php

namespace App\Assessments\Repository;

use App\Assessments\Entity\AssessmentSequence;

use Beerfranz\RogerBundle\Repository\RogerRepositorySequenceTrait;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AssessmentSequence>
 *
 * @method AssessmentSequence|null find($id, $lockMode = null, $lockVersion = null)
 * @method AssessmentSequence|null findOneBy(array $criteria, array $orderBy = null)
 * @method AssessmentSequence[]    findAll()
 * @method AssessmentSequence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssessmentSequenceRepository extends ServiceEntityRepository
{

	use RogerRepositorySequenceTrait;
	
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, AssessmentSequence::class);
	}

}
