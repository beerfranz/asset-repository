<?php

namespace App\Assessments\Doctrine;

use App\Assessments\Entity\AssessmentPlan;
use App\Assessments\Entity\AssessmentSequence;

use Beerfranz\RogerBundle\Entity\RogerEntity;
use Beerfranz\RogerBundle\Doctrine\RogerListener;

use Doctrine\ORM\Event\PrePersistEventArgs;
use Beerfranz\RogerBundle\Doctrine\RogerListenerFacade;

class PlanListener
{

	public function prePersist(RogerEntity $entity, PrePersistEventArgs $event)
	{
		$entityManager = $event->getObjectManager();
		
		$seqRepo = $entityManager->getRepository($entity->getSequenceClass());

		$props = [];
		foreach($entity->getSequencedProperties() as $prop => $attr) {
			if ($entity->__get($prop) === null)
				$props[$prop] = $attr;
		}

		if (count($props) > 0) {
			$newSeq = $seqRepo->increment(1);

			foreach($props as $prop => $attr) {
				$value = $newSeq;
				if (isset($attr['prefix']))
					$value = $attr['prefix'] . $value;

				$entity->__set($prop, $value);
			}
		}
	}

}
