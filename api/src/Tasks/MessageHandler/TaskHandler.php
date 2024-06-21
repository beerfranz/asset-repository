<?php

namespace App\Tasks\MessageHandler;

use App\Indicators\Message\IndicatorValueMessage;
use App\Assessments\Entity\AssessmentTemplate;
use App\Assessments\Entity\AssessmentPlan;

use Beerfranz\RogerBundle\Message\RogerAsyncMessage;
use Beerfranz\RogerBundle\MessageHandler\RogerHandlerAbstract;
use App\Tasks\Service\TaskService;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Psr\Log\LoggerInterface;


class TaskHandler extends RogerHandlerAbstract
{

	public function __construct(
		protected TaskService $service,
		protected LoggerInterface $logger,
		protected EntityManagerInterface $entityManager,
	)
	{

	}

	#[AsMessageHandler]
	public function indicatorValueMessage(IndicatorValueMessage $message)
	{
		$this->handlerName = __METHOD__;
		$this->messageClass = $message::class;

		$this->logReceiveMessage();

		$event = $message->getEvent();
		$context = $message->getContext();

		if ($this->isAboutEntityNames($context, [ 'IndicatorValue' ]) && isset($context['entity']['indicator']['taskTemplateIdentifier'])) {
			$this->logProcessingMessage();
			$messageIndicatorValue = $context['entity'];
			$messageIndicator = $messageIndicatorValue['indicator'];
			$messageTaskTemplateIdentifier = $messageIndicator['taskTemplateIdentifier'];

			$taskTemplate = $this->service->findOneTaskTemplateByIdentifier($messageTaskTemplateIdentifier);

			$properties = [];
			$properties['attributes'] = [
				'indicator' => [
					'identifier' => $messageIndicator['identifier'],
				],
				'indicatorValue' => [
					'identifier' => $messageIndicatorValue['identifier'],
				],
				'relatedTo' => [
					'indicatorValue' => [
						'value' => '/indicators/' . $messageIndicator['identifier'] . '/values/' . $messageIndicatorValue['identifier'],
						'kind' => 'link',
						'title' => 'Indicator Value',
					]
				]
			];

			try {
				$task = $this->service->generateTaskFromTaskTemplate(
					$taskTemplate,
					$messageIndicator['identifier'] . '_' . $messageIndicatorValue['identifier'],
					null,
					$properties,
				);
			} catch (\Exception $e) {
				$this->logger->error('Failed to generate task for indicator value ' . $messageIndicator['identifier'] . '_' . $messageIndicatorValue['identifier']);
				throw $e;
			}
		} else {
			$this->logIgnoringMessage('not about entity IndicatorValue');
		}
	}

	#[AsMessageHandler]
	public function assessmentPlanMessage(RogerAsyncMessage $message)
	{
		$this->handlerName = __METHOD__;
		$this->messageClass = $message::class;

		$event = $message->getEvent();
		$context = $message->getContext();

		if ($this->isAboutEntityNames($context, [ 'AssessmentPlan' ]) && $context['action'] === 'create') {
			$this->logProcessingMessage();

			if (! isset($context['entity']['assessmentTemplate']['id'])) {
				$this->logIgnoringMessage('No assessmentTemplate id in message');
				return;
			}

			$assessmentTemplateId = $context['entity']['assessmentTemplate']['id'];
			$assessmentTemplateRepo = $this->entityManager->getRepository(AssessmentTemplate::class);
			$assessmentTemplate = $assessmentTemplateRepo->find($assessmentTemplateId);

			if ($assessmentTemplate === null) {
				$this->logIgnoringMessage('No assessment template find');
				return;
			}

			$assessmentPlanRepo = $this->entityManager->getRepository(AssessmentPlan::class);
			$assessmentPlan = $assessmentPlanRepo->find($context['entity']['id']);

			if ($assessmentPlan === null) {
				$this->logIgnoringMessage('No assessment plan find');
				return;
			}

			foreach ($assessmentTemplate->getTaskTemplates() as $taskTemplate) {

				$task = $this->service->generateTaskFromTaskTemplate($taskTemplate, null, null, []);

				$assessmentPlan->addTask($task);
			}
			$this->entityManager->persist($assessmentPlan);
			$this->entityManager->flush();
		} else {
			$this->logIgnoringMessage('No create AssessmentPlan');
		}
	}

}