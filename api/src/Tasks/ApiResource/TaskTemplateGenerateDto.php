<?php

namespace App\Tasks\ApiResource;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class TaskTemplateGenerateDto
{
	#[Groups(['TaskTemplateGenerate'])]
	public ?string $identifier = null;
}
