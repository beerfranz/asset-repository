<?php

namespace App\State;

use App\Entity\Owner;
use App\Entity\Source;
use App\Entity\Version;
use App\Entity\Kind;

use App\Repository\RogerRepositoryInterface;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RequestStack;

use Psr\Log\LoggerInterface;

abstract class CommonState {
	protected $logger;
    protected $entityManager;
    protected $request;
    protected $security;

    public function __construct(
        EntityManagerInterface $entityManager,
        RequestStack $request,
        LoggerInterface $logger,
        Security $security
    ) {
        $this->entityManager = $entityManager;
        $this->request = $request->getCurrentRequest();
        $this->logger = $logger;
        $this->security = $security;
    }

    protected function getCollection($repo, $context)
    {
        // Default values
        $page = 1;
        $itemsPerPage = 10;
        $order = [ 'id' => 'asc'];
        $criteria = [];

        if (isset($context['filters'])) {

            foreach ($context['filters'] as $filter => $value)
            {
                switch($filter) {
                    case '_': break;
                    case 'page':
                    case 'itemsPerPage':
                    case 'order':
                        $$filter = $value;
                        break;
                    default:
                        $criteria[$filter] = $value;
                }
            }
        }

        if ($repo instanceof RogerRepositoryInterface) {
            return $repo->rogerFindBy($criteria, $order, $itemsPerPage, ($page - 1) * $itemsPerPage, true);
        }

        return $repo->findBy($criteria, $order, $itemsPerPage, ($page - 1) * $itemsPerPage);

    }

    protected function setOwner($object, $name)
    {
        if (isset($name['identifier']))
            $name = $name['identifier'];

    	if ($name !== null)
        {
            $repoOwner = $this->entityManager->getRepository(Owner::class);
            $owner = $repoOwner->findOneByName($name);

            // If no owner find in the DB, create an owner
            if ($owner === null)
            {
                $owner = new Owner();
                $owner->setName($name);
            }

            $object->setOwner($owner);
        }
        return $object;
    }

    protected function setSource($object, $name)
    {
        if ($object->getSource() !== null)
            return $object;
        
        if ($name !== null)
        {
            $repoSource = $this->entityManager->getRepository(Source::class);
            $source = $repoSource->findOneByName($name);

            // If no source find in the DB, create a source
            if ($source === null)
            {
                $source = new Source();
                $source->setName($name);
            }

            $object->setSource($source);
        }
        return $object;
    }

    protected function setAttributes($object, $input)
    {
    	if (isset($input['attributes'])) {
            // Attributes must be grouped
            $attributes = [];
            foreach ($input['attributes'] as $key => $value) {
                if (is_iterable($value)) {
                    $attributes[$key] = $value;
                } else {
                    $attributes['uncategorized'][$key] = $value;
                }
            }
            $object->setAttributes($attributes);
        }

        return $object;
    }

    protected function setLabels($object, $input)
    {
    	if (isset($input['labels']))
            $object->setLabels($input['labels']);

        return $object;
    }

    protected function setVersion($object, $name)
    {
        if ($name !== null)
        {
            $versionRepo = $this->entityManager->getRepository(Version::class);
            $version = $versionRepo->findOneByAssetDefinitionAndName($object->getAssetDefinition(), $name);

            // If no source find in the DB, create a source
            if ($version === null)
            {
                $version = new Version();
                $version->setName($name);
                $version->setAssetDefinition($object->getAssetDefinition());
                $this->entityManager->persist($version);
            }

            $object->setVersion($version);
        }
        return $object;
    }

    protected function setKindByIdentifier($object, $identifier)
    {
        $kindRepo = $this->entityManager->getRepository(Kind::class);
        $kind = $kindRepo->findOneByIdentifier($identifier);

        // If no kind find in the DB, create a kind
        if ($kind === null)
        {
            $kind = new Kind();
            $kind->setIdentifier($identifier);
            $this->entityManager->persist($kind);
        }

        $object->setKind($kind);
    }

    protected function deleteEntity($entity)
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }
}
