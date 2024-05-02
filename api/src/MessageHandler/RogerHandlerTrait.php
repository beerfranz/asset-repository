<?php

namespace App\MessageHandler;


trait RogerHandlerTrait {


	protected function isAboutEntityNames($context, array $entityNames = []): bool
	{
        $className = $this->getContextEntityClassName($context);

        if ($className === null)
        	return false;
        
        if (in_array($className, $entityNames))
        	return true;

        return false;
	}

	protected function getContextEntityClassName($context): ?string
	{
		if (!isset($context['class']))
			return null;

		$class_array = explode('\\', $context['class']);
        return end($class_array);
	}

	protected function getContextAction($context): ?string
	{
		if (!isset($context['action']))
			return null;
		return $context['action'];
	}

	protected function reDispatch($envelop)
	{
      	$this->eventBus->dispatch(
          (new Envelope($envelop))
              ->with(new DispatchAfterCurrentBusStamp()));
	}

	protected function logReceiveMessage(): void
	{
		$this->logger->debug('Message ' . $this->messageClass . ' received by ' . $this->handlerName . '.');
	}

	protected function logProcessingMessage(): void
	{
		$this->logger->debug('Message ' . $this->messageClass . ' processing by ' . $this->handlerName . '.');
	}

	protected function logIgnoringMessage(string $reason = '???'): void
	{
		$this->logger->debug('Message ' . $this->messageClass . ' ignored by ' . $this->handlerName . ' because ' . $reason);
	}
	
}
