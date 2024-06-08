<?php

namespace App\Assets\ApiResource;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class AssetBatchDto
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
	public array $assets = [];

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

	#[Assert\Collection(
		fields: [
			'identifier' => new Assert\Required(),
		]
	)]
	#[Groups(['Asset:write'])]
	public ?array $kind = null;

	#[Groups(['Asset:write'])]
	public function getAssets() : array
	{
		$results = [];
		$defaultData = [
			'owner' => $this->owner,
			'source' => $this->source,
			'kind' => $this->kind,
		];
		foreach($this->assets as $key => $asset) {
			$results[] = array_merge($defaultData, $asset);
		}
		return $results;
	}

	public function getId()
	{
		return 1;
	}
}
