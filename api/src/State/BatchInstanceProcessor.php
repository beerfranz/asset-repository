<?php

namespace App\State;

use App\Entity\Asset;
use App\Entity\Instance;
use App\Entity\Source;
use App\ApiResource\BatchInstance;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RequestStack;


use Psr\Log\LoggerInterface;

final class BatchInstanceProcessor extends CommonState implements ProcessorInterface
{

    /**
     * @param BatchInstanceDto $data
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $repo = $this->entityManager->getRepository(Instance::class);
        $repoAsset = $this->entityManager->getRepository(Asset::class);
        $repoSource = $this->entityManager->getRepository(Source::class);

        $source = $repoSource->findOneByName($data->source);

        $identifiers = [];

        $user = $this->security->getUser();

        foreach($data->instances as $input)
        {
            $identifier = $input['identifier'];

            $instance = $repo->findOneByIdentifier($identifier);

            if ($instance === null)
            {
                $instance = new Instance();
                $instance->setIdentifier($identifier);
            }

            $this->setSource($instance, $data->source);
            
            // $instance = $this->setOwner($instance, $input, $repoOwner);

            // $instance = $this->setLabels($instance, $input);
            $instance = $this->setAttributes($instance, $input);
            $instance->setVersion($input['version']);
            // $instance = $this->setVersion($instance, $input);

            if (isset($input['asset']))
            {
                $asset = $repoAsset->findOneByIdentifier($input['asset']);
                $instance->setAsset($asset);
            }

            $identifiers[] = $instance->getIdentifier();

            $this->entityManager->persist($instance);
            $this->entityManager->flush();
        }

        // For PUT request, remove instances not present
        if ($operation instanceof Put && $data->source !== null)
        {
            // Get instances
            $instancesToRemove = $repo->findInstancesByIdentifiersNotIn($identifiers, [ 'source' => $source->getId() ]);;
            foreach ($instancesToRemove as $instance)
            {
                // Delete the instance
                $this->entityManager->remove($instance);
            }
            $this->entityManager->flush();
        }
    }
}
