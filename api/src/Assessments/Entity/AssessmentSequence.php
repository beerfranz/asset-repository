<?php

namespace App\Assessments\Entity;

use App\Assessments\Repository\AssessmentSequenceRepository;

use Doctrine\ORM\Mapping as ORM;

use Beerfranz\RogerBundle\Entity\RogerSequence;

#[ORM\Entity(repositoryClass: AssessmentSequenceRepository::class)]
class AssessmentSequence extends RogerSequence
{

}
