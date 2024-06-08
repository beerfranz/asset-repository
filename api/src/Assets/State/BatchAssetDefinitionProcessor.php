<?php

namespace App\Assets\State;

use App\Assets\Entity\AssetDefinition;
use App\Assets\Entity\AssetDefinitionRelation;
use App\Assets\Entity\Owner;
use App\Assets\Entity\Source;
use App\Assets\Entity\EnvironmentDefinition;

use App\Assets\ApiResource\BatchAssetDefinition;
use App\Assets\State\CommonState;
use App\Assets\State\TraitDefinitionPropagate;
use App\Assets\State\AssetDefinitionState;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RequestStack;


use Psr\Log\LoggerInterface;

final class BatchAssetDefinitionProcessor extends CommonState implements ProcessorInterface
{
	use TraitDefinitionPropagate;

	protected $assetDefinitionState;

	public function __construct(
		EntityManagerInterface $entityManager,
		RequestStack $request,
		LoggerInterface $logger,
		Security $security,
		AssetDefinitionState $assetDefinitionState,
	) {
		parent::__construct($entityManager, $request, $logger, $security);
		$this->assetDefinitionState = $assetDefinitionState;
	}

	/**
	 * @param BatchAssetDefinitionDto $data
	 * @return T2
	 */
	public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
	{
		$repo = $this->entityManager->getRepository(AssetDefinition::class);
		$repoRelation = $this->entityManager->getRepository(AssetDefinitionRelation::class);
		$repoOwner = $this->entityManager->getRepository(Owner::class);
		$repoSource = $this->entityManager->getRepository(Source::class);

		$source = $repoSource->findOneByName($data->source);

		$identifiers = [];

		$user = $this->security->getUser();

		foreach($data->assetDefinitions as $input)
		{
			if (!isset($input['owner']))
				$input['owner'] = $data->owner;

			if (!isset($input['source']))
				$input['source'] = $data->source;

			$assetDefinition = $this->assetDefinitionState->processOneAssetDefinition($input);
			$identifiers[] = $assetDefinition->getIdentifier();

			if (isset($input['relations']))
			{
				foreach($input['relations'] as $relation)
				{
					if (isset($relation['identifier']) && isset($relation['relation'])) {
						$assetDefinitionRelationTo = $repo->findOneByIdentifier($relation['identifier']);

						// Add only minimal data
						if ($assetDefinitionRelationTo === null) {
							throw new \Exception('Cannot create a relation between ' . $assetDefinition->getIdentifier() . ' and ' . $relation['identifier'] . '. No AssetDefinition with the identifier ' . $relation['identifier'] . '. You must create the AssetDefinition before.');
						}

						$this->assetDefinitionState->processOneAssetDefinitionRelation($assetDefinition, $assetDefinitionRelationTo, $relation);
					}
				}
			}

			$this->updateAssets($assetDefinition);
		}

		// For PUT request, remove assets not present
		if ($operation instanceof Put && $data->source !== null)
		{
			// Get assets
			$assetDefinitionsToRemove = $repo->findAssetDefinitionsByidentifiersNotIn($identifiers, [ 'source' => $source->getId() ] );
			foreach ($assetDefinitionsToRemove as $assetDefinition)
			{
				foreach ($assetDefinition->getAssets() as $asset) {
					$asset->setAssetDefinition(null);
					$this->entityManager->persist($asset);
				}

				// Delete the asset
				$this->entityManager->remove($assetDefinition);
			}
			$this->entityManager->flush();
		}

		// Generate assets
		
	}
}
