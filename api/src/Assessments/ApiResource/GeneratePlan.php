<?php

namespace App\Assessments\ApiResource;

use Beerfranz\RogerBundle\ApiResource\RogerApiResource;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class GeneratePlan extends RogerApiResource
{
	#[Groups(['AssessmentTemplateGenerate'])]
	public ?string $identifier = null;

	#[Groups(['AssessmentTemplateGenerate'])]
	public array $assets = [];
}
