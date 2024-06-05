<?php

namespace App\Assessments\State;

use App\Assessments\ApiResource\Template as TemplateApi;
use App\Assessments\ApiResource\GeneratePlan;

use App\Assessments\Entity\AssessmentTemplate as TemplateEntity;

use App\Assessments\Service\TemplateService;
use ApiPlatform\Metadata\Operation;

use Beerfranz\RogerBundle\State\RogerState;
use Beerfranz\RogerBundle\State\RogerStateFacade;

final class TemplateState extends RogerState
{

	public function __construct(
		RogerStateFacade $facade,
		TemplateService $service,
	) {
		parent::__construct($facade, $service);
	}

	public function newApi(): TemplateApi
	{
		return new TemplateApi();
	}

	public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
	{
		$operationInputs = $operation->getInput();
		if (isset($operationInputs['name']) && $operationInputs['name'] === 'GeneratePlan')
			return new GeneratePlan();
		
		return $this->stateProvide($operation, $uriVariables, $context);
	}

	/**
	 * @param $data
	 * @return T2
	 */
	public function process(mixed $api, Operation $operation, array $uriVariables = [], array $context = [])
	{
		if ($api instanceof GeneratePlan)
		{
			$templateIdentifier = $uriVariables['identifier'];

			$template = $this->getEntityByIdentifier($templateIdentifier);

			$plans = [];
			foreach ($api->__get('assets') as $assetIdentifier) {
				$plan = $this->service->generatePlanFromTemplate($template, $assetIdentifier);
				$plans[] = $plan->getIdentifier();
			}

			return [ 'plans' => $plans ];
		}
		
		return $this->stateProcess($api, $operation, $uriVariables, $context);
	}

	public function fromApiToEntity($api, $entity): TemplateEntity
	{
		// if ($entity->getIdentifier() === null)
			$entity->setIdentifier($api->__get('identifier'));

		$entity->setTitle($api->title);

		foreach ($api->assets as $assetIdentifier) {
			$entity->addAsset($this->service->findOneAssetByIdentifier($assetIdentifier));
		}

		foreach ($api->taskTemplates as $taskTemplateIdentifier) {
			$entity->addTaskTemplate($this->service->findOneTaskTemplateByIdentifier($taskTemplateIdentifier));
		}
		
		return $entity;
	}

	public function fromEntityToApi($entity, $api): TemplateApi
	{
		$api->identifier = $entity->getIdentifier();
		$api->title = $entity->getTitle();

		$api->assetsCount = count($entity->getAssets());
		foreach ($entity->getAssets() as $asset) {
			$api->assets[] = $asset->getIdentifier();
		}

		$api->taskTemplatesCount = count($entity->getTaskTemplates());

		foreach ($entity->getTaskTemplates() as $taskTemplate) {
			$api->taskTemplates[] = $taskTemplate->getIdentifier();
		}

		return $api;
	}

}
