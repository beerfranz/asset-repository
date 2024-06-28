<?php

namespace App\Risks\Service;

use App\Risks\Entity\Risk;
use App\Risks\Entity\RiskManager;
use App\Assets\Entity\Asset;

use App\Common\Service\UserTemplate;

use Beerfranz\RogerBundle\Service\RogerService;

use Doctrine\ORM\EntityManagerInterface;

use Psr\Log\LoggerInterface;

class RiskService extends RogerService
{
	protected $assetRepo;
	protected $riskManagerRepo;

	public function __construct(
		EntityManagerInterface $entityManager,
		LoggerInterface $logger,
		protected UserTemplate $userTemplate,
	) {
		parent::__construct($entityManager, $logger, Risk::class);

		$this->assetRepo = $entityManager->getRepository(Asset::class);
		$this->riskManagerRepo = $entityManager->getRepository(RiskManager::class);
	}

	public function newEntity(): Risk
	{
		$entity = new Risk();

		return $entity;
	}

	public function findOneAssetByIdentifier($identifier) {
		return $this->findOneByIdentifierInRepo($identifier, $this->assetRepo);
	}

	public function findOneRiskManagerByIdentifier($identifier) {
		return $this->findOneByIdentifierInRepo($identifier, $this->riskManagerRepo);
	}

	public function aggregateRisk(string $formula, array $values, array $triggers = []): array
	{
		$userTemplate = $this->userTemplate->template('{{ ' . $formula . '}}', $values);
		$aggregatedRisk = $userTemplate->getResult();
		$trigger = $this->getTrigger($triggers, $aggregatedRisk);

		return [ 'value' => $aggregatedRisk, 'trigger' => $trigger ];
	}

	protected function getTrigger($triggers, $riskValue)
	{
		$triggersToTest = [ 'danger', 'warning', 'info' ];

		$trigger = '';

		foreach($triggersToTest as $triggerToTest) {
			if (isset($triggers[$triggerToTest])) {
				$userTemplate = $this->userTemplate->test($triggers[$triggerToTest], [ 'aggregator' => $riskValue ]);
				if ($userTemplate->getBoolResult()) {
					$trigger = $triggerToTest;
					break;
				}
			}
		}

		return $trigger;
	}
}