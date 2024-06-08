<?php

namespace App\Tests\Factory;

use App\Tests\Factory\RogerFactory;

final class AssessmentFactory extends RogerFactory
{

	public static function getAssessmentPlan($options = [])
	{
		$e = [
			'title' => $this->randomString(),
		];

		return $e;
	}

	public static function getAssessmentTemplate($options = [])
	{
		$e = [
			'title' => $this->randomString(),
		];

		return $e;
	}

}
