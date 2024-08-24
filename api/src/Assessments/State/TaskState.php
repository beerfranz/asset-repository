<?php

namespace App\Assessments\State;

use App\Tasks\State\TaskState as TaskStateBase;

use App\Assessments\ApiResource\Plan as PlanApi;
use App\Assessments\Entity\AssessmentPlan as PlanEntity;

use App\Assessments\Service\PlanService;

use Beerfranz\RogerBundle\State\RogerState;
use Beerfranz\RogerBundle\State\RogerStateFacade;

final class TaskState extends TaskStateBase
{

	public function __construct(
		RogerStateFacade $facade,
		PlanService $service,
	) {
		parent::__construct($facade, $service);
	}

}
