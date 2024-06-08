<?php

namespace App\Assets\ApiResource;

use App\Assets\State\VersionState;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;

use ApiPlatform\Serializer\Filter\GroupFilter;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ApiResource(
	description: 'Versions',
	processor: VersionState::class,
	provider: VersionState::class,
	normalizationContext: ['groups' => ['Version:read']],
	denormalizationContext: ['groups' => ['Version:write']],
)]
#[GetCollection(security: "is_granted('ASSET_READ')")]
#[Get(security: "is_granted('ASSET_READ')")]
#[Post(security: "is_granted('ASSET_WRITE')")]
#[Put(security: "is_granted('ASSET_WRITE')")]
#[ApiFilter(GroupFilter::class, arguments: ['parameterName' => 'groups', 'overrideDefaultGroups' => true, 'whitelist' => ['Version:name']])]
class Version
{
	#[Groups(['Version:read'])]
	public $id;

	#[Groups(['Version:read', 'Version:write', 'AssetDefinition:read'])]
	#[ApiProperty(identifier: true)]
	public $identifier;

	#[Groups(['Version:read', 'Version:write'])]
	public $name;

	#[Groups(['Version:read', 'Version:write'])]
	public $version;

	#[Groups(['Version:read'])]
	public array $assetDefinitions = [];

	public function getId() {
		return $this->identifier;
	}

}
