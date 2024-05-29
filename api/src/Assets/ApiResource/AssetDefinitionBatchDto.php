<?php

namespace App\Assets\ApiResource;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class AssetDefinitionBatchDto
{
	#[Assert\All([new Assert\Collection(
		fields: [
			'identifier' => new Assert\Required([
				new Assert\NotBlank
			]),
			'kind' => new Assert\Collection(
				fields: [ 'identifier' => new Assert\Required(new Assert\NotBlank),
			]),
		],
		allowExtraFields: true
	)])]
	#[Groups(['Asset:read', 'Asset:write'])]
	public array $assetDefinitions = [];

	#[Groups(['Asset:read', 'Asset:write'])]
	public ?string $source = null;

	#[Assert\Collection(
		fields: [
			'identifier' => new Assert\Required([
				new Assert\NotBlank
			])
		]
	)]
	#[Groups(['Asset:write'])]
	public array $owner;

	public function getAssetDefinitions() : array
	{
		$results = [];
		$defaultData = [
			'owner' => $this->owner,
			'source' => $this->source,
		];
		foreach($this->assetDefinitions as $key => $assetDefinition) {
			$results[] = array_merge($defaultData, $assetDefinition);
		}
		return $results;
	}

}
