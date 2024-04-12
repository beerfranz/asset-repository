<?php
namespace App\Doctrine;

use App\Entity\IndicatorValue;
use App\Message\IndicatorValueMessage;

use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;

use Psr\Log\LoggerInterface;

class IndicatorValueListener
{

    public function __construct(
        private Security $security,
        private SerializerInterface $serializer,
        private LoggerInterface $logger,
        private MessageBusInterface $bus,
    )
    {
    }


    public function postPersist(IndicatorValue $entity, PostPersistEventArgs $event)
    {   
        $this->bus->dispatch(new IndicatorValueMessage('create_indicator_value', [ 'IndicatorValue' => $this->serializer->normalize($entity) ]));
    }

    public function postRemove(IndicatorValue $entity, PostRemoveEventArgs $event)
    {
        $this->bus->dispatch(new IndicatorValueMessage('remove_indicator_value', [ 'IndicatorValue' => $this->serializer->normalize($entity) ]));
    }

    public function postUpdate(IndicatorValue $entity, PostUpdateEventArgs $event)
    {
        $this->bus->dispatch(new IndicatorValueMessage('update_indicator_value', [ 'IndicatorValue' => $this->serializer->normalize($entity) ]));
    }
}
