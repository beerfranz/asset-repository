<?php
namespace App\Doctrine;

use App\Entity\Asset;
use App\Entity\AssetAudit;

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

use Psr\Log\LoggerInterface;
// use Symfony\Bridge\Monolog\Logger;

class AssetListener
{

    private $security;
    private $serializer;
    private $logger;

    public function __construct(Security $security, SerializerInterface $serializer, LoggerInterface $logger)
    {
        $this->security = $security;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    public function prePersist(Asset $asset)
    {
        $user = $this->security->getUser();
        $email = $user->getEmail();
        // Init a new data
        if ($asset->getId() === null) {
            $asset->setCreatedBy($email);
            $asset->setCreatedAt(new \DateTimeImmutable());
            $asset->setOwner($email);
        }
    }

    public function postPersist(Asset $asset, PostPersistEventArgs $event)
    {
        $entityManager = $event->getObjectManager();
        
        $diff = $this->serializer->normalize($asset, null);

        $user = $this->security->getUser();
        $email = $user->getEmail();

        $audit = new AssetAudit();
        $audit->setDatetime(new \DateTimeImmutable)
              ->setAction('create')
              ->setSubject($asset->getIdentifier())
              ->setActor($email)
              ->setData($diff)
        ;

        $entityManager->persist($audit);
        $entityManager->flush();

        $this->logger->error('I just got the logger');
    }

    public function postRemove(Asset $asset, PostRemoveEventArgs $event)
    {
        $entityManager = $event->getObjectManager();

        $diff = $this->serializer->normalize($asset, null, ['iri' => 'removed']);

        $user = $this->security->getUser();
        $email = $user->getEmail();

        $audit = new AssetAudit();
        $audit->setDatetime(new \DateTimeImmutable)
              ->setActor($email)
              ->setAction('remove')
              ->setSubject($asset->getIdentifier())
              ->setData($diff)
        ;

        $entityManager->persist($audit);
        $entityManager->flush();
    }

    public function postUpdate(Asset $asset, PostUpdateEventArgs $event)
    {
        $entityManager = $event->getObjectManager();
        $diff = $entityManager->getUnitOfWork()->getEntityChangeSet($asset);

        $user = $this->security->getUser();
        $email = $user->getEmail();

        $audit = new AssetAudit();
        $audit->setDatetime(new \DateTimeImmutable)
              ->setActor($email)
              ->setAction('update')
              ->setSubject($asset->getIdentifier())
              ->setData(['diff' => $diff ]);

        $entityManager->persist($audit);
        $entityManager->flush();
    }
}
