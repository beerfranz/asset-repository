<?php

namespace Beerfranz\RogerBundle\Doctrine;

use Beerfranz\RogerBundle\Entity\RogerEntity;
use Beerfranz\RogerBundle\Doctrine\RogerListenerFacade;
use Beerfranz\RogerBundle\Message\RogerAsyncMessage;

use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\SerializerInterface;

class RogerListener
{
	protected $logger;
	protected $serializer;
	protected $security;
	protected $user;
	protected $bus;

	protected $action;
	protected $entity;
	protected $diff;

	public function __construct(
		protected RogerListenerFacade $facade,
	)
	{
		$this->logger = $this->facade->getLogger();
		$this->serializer = $this->facade->getSerializer();
		$this->security = $this->facade->getSecurity();
		$this->user = $this->security->getUser();
		$this->bus = $this->facade->getBus();
	}

	public function postPersist(RogerEntity $entity, PostPersistEventArgs $event)
	{
		$entityManager = $event->getObjectManager();
		
		$this->action = 'create';
		$this->entity = $entity;
		$this->diff = $this->serializer->normalize($entity, null);

		$this->logChange();
		$this->sendMessage();
	}

	public function postRemove(RogerEntity $entity, PostRemoveEventArgs $event)
	{
		$entityManager = $event->getObjectManager();
		$diff = $this->serializer->normalize($event->getObject(), null);

		$this->action = 'remove';
		$this->diff = $diff;
		$this->entity = $entity;
		
		$this->logChange();
		$this->sendMessage();
	}

	public function postUpdate(RogerEntity $entity, PostUpdateEventArgs $event)
	{
		$entityManager = $event->getObjectManager();
		$diffs = $entityManager->getUnitOfWork()->getEntityChangeSet($entity);

		foreach($diffs as $name => $value) {
			$diffs[$name] = $this->serializer->normalize($value, null);
		}

		// if ($diffs === [])
		//     return true;

		$this->action = 'update';
		$this->diff = $diffs;
		$this->entity = $entity;

		$this->logChange();
		$this->sendMessage([ 'diffs' => $diffs ]);
		
	}

	protected function getUser()
	{
		if ($this->user !== null && $this->user->getId())
			return $this->user->getId();

		return 'unknown';
	}

	protected function logChange() {
		$this->logger->info($this->action . ' entity ' . $this->entity::class . ' by user ' . $this->getUser());
	}

	protected function sendMessage(array $context = []) {
		$context['action'] = $this->action;
		$context['class'] = $this->entity::class;
		$class_array = explode('\\', $context['class']);
		$context['className'] = end($class_array);
		// $context['entity'] = $this->serializer->normalize($this->entity, null);
		$context['entity'] = $this->entity->toArray();
		// $this->logger->error('entity :' . print_r($context['entity']));
		// exit;
		$context['entity'] = $this->entity->toArray();
		$context['actor'] = $this->getUser();
		$context['datetime'] = new \DateTimeImmutable();

		$messageClass = '\\App\\Message\\' . $context['className'] . 'Message';
		if (!class_exists($messageClass))
			$messageClass = '\\Beerfranz\\RogerBundle\\Message\\RogerAsyncMessage';

		$this->logger->info('Dispatch message ' . $messageClass . ': ' . $this->action . ' ' . $this->entity::class);
		$this->bus->dispatch(new $messageClass($this->action, $context));
	}
}