<?php

namespace App\Assets\ApiResource;

use App\Assets\ApiResource\AssetDefinitionBatchDto;
use App\State\AssetDefinitionState;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ApiResource(
	description: 'Asset definitions',
	processor: AssetDefinitionState::class,
	provider: AssetDefinitionState::class,
	denormalizationContext: ['groups' => ['Asset:write']],
)]
#[Put(name: 'put_asset_definition_by_source', uriTemplate: '/sources/{sourceId}/asset_definitions', uriVariables: [ 'sourceId' ], 
	security: "is_granted('ASSET_WRITE')", input: AssetDefinitionBatchDto::class, output: AssetDefinitionBatchDto::class,)]
#[Post(name: 'post_asset_definition_by_source', uriTemplate: '/sources/{sourceId}/asset_definitions', uriVariables: [ 'sourceId' ] ,
	security: "is_granted('ASSET_WRITE')", input: AssetDefinitionBatchDto::class, output: AssetDefinitionBatchDto::class)]
class AssetDefinition
{
	#[Groups(['Asset:write'])]
	public $assetDefinitions;

	#[Groups(['Asset:write'])]
	public $source = null;

	#[Groups(['Asset:write'])]
	public $owner = null;
}
