<?php
namespace App\Assets\Doctrine;

use App\Assets\Entity\Asset;
use App\Assets\Entity\AssetAudit;
use App\Assets\Entity\Owner;

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
        }
    }

    public function postPersist(Asset $asset, PostPersistEventArgs $event)
    {
        $entityManager = $event->getObjectManager();
        
        $diff = $this->serializer->normalize($asset, null, ['groups' => 'Asset:read' ]);

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
    }

    public function postRemove(Asset $asset, PostRemoveEventArgs $event)
    {
        $entityManager = $event->getObjectManager();
        $diff = $this->serializer->normalize($event->getObject(), null, ['groups' => 'Asset:read' ,'iri' => 'removed']);

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
        $diffs = $entityManager->getUnitOfWork()->getEntityChangeSet($asset);

        foreach($diffs as $name => $value) {
            $diffs[$name] = $this->serializer->normalize($value, null, ['groups' => 'Asset:read']);
        }

        // if ($diffs === [])
        //     return true;

        $user = $this->security->getUser();
        $email = $user->getEmail();

        $audit = new AssetAudit();
        $audit->setDatetime(new \DateTimeImmutable)
              ->setActor($email)
              ->setAction('update')
              ->setSubject($asset->getIdentifier())
              ->setData(['diff' => $diffs ]);

        $entityManager->persist($audit);
        $entityManager->flush();
    }
}
