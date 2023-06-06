<?php

namespace App\State;

use App\Entity\Asset;
use App\Entity\Instance;
use App\Entity\Source;
use App\ApiResource\Instance as InstanceDto;
use App\ApiResource\InstanceBatchDto;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\State\ProviderInterface;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RequestStack;


use Psr\Log\LoggerInterface;

final class InstanceState extends CommonState implements ProcessorInterface, ProviderInterface
{
    protected $instanceRepo;
    protected $assetRepo;
    protected $sourceRepo;

    public function __construct(
        EntityManagerInterface $entityManager,
        RequestStack $request,
        LoggerInterface $logger,
        Security $security,
    ) {
        parent::__construct($entityManager, $request, $logger, $security);
        $this->instanceRepo = $entityManager->getRepository(Instance::class);
        $this->assetRepo = $entityManager->getRepository(Asset::class);
        $this->sourceRepo = $entityManager->getRepository(Source::class);
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface)
        {
            return $this->getCollection($this->instanceRepo, $context);
        }

        if ($context['input']['class'] === InstanceBatchDto::class) {
            $output = new InstanceBatchDto();
            return $output;
        }
        
        return $this->instanceRepo->findOneByIdentifier(null, $uriVariables['name']);
    }

    /**
     * @param $data
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if ($data instanceof InstanceBatchDto) {

            $identifiers = [];

            foreach($data->instances as $input)
            {
                $identifier = $input['identifier'];

                if (isset($uriVariables['sourceId'])) {
                    $input['source'] = $uriVariables['sourceId'];
                }

                $identifiers[] = $this->processOneInstance($input)->getIdentifier();
            }

            if ($operation instanceof Put && $uriVariables['sourceId'] !== null)
            {
                $source = $this->sourceRepo->findOneByName($uriVariables['sourceId']);
            
                $instancesToRemove = $this->instanceRepo->findInstancesByIdentifiersNotIn($identifiers, [ 'source' => $source->getId() ]);
                foreach ($instancesToRemove as $instance)
                {
                    // Delete the instance
                    $this->entityManager->remove($instance);
                }
                $this->entityManager->flush();
            }

        }
    }

    protected function processOneInstance($data): Instance
    {
        $identifier = $data['identifier'];

        $instance = $this->instanceRepo->findOneByIdentifier($identifier);

        if ($instance === null)
        {
            $instance = new Instance();
            $instance->setIdentifier($identifier);
        }

        if (isset($data['source']))
            $this->setSource($instance, $data['source']);
        
        // $instance = $this->setOwner($instance, $input, $repoOwner);

        // $instance = $this->setLabels($instance, $input);
        $instance = $this->setAttributes($instance, $data);
        $instance->setVersion($data['version']);
        // $instance = $this->setVersion($instance, $input);

        if (isset($data['asset']))
        {
            $asset = $this->assetRepo->findOneByIdentifier($data['asset']);
            $instance->setAsset($asset);
        }

        $this->entityManager->persist($instance);
        $this->entityManager->flush();

        return $instance;
    }
}