<?php

namespace App\Doctrine;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;

class RogerListenerFacade
{

    public function __construct(
        protected Security $security,
        protected SerializerInterface $serializer,
        protected LoggerInterface $logger,
        protected MessageBusInterface $bus,
    ) {
        
    }

    public function getSecurity() {
        return $this->security;
    }

    public function getSerializer() {
        return $this->serializer;
    }

    public function getLogger() {
        return $this->logger;;
    }

    public function getBus() {
        return $this->bus;
    }
}
