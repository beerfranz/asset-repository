<?php

namespace App\Assets\State;

use App\Assets\Entity\EnvironmentDefinition;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RequestStack;


use Psr\Log\LoggerInterface;

final class EnvironmentDefinitionProcessor extends CommonState implements ProcessorInterface
{

	/**
	 * @param EnvironmentDefinitionDto $data
	 * @return T2
	 */
	public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
	{
		$repo = $this->entityManager->getRepository(EnvironmentDefinition::class);

		$identifiers = [];

		$user = $this->security->getUser();

		$identifier = $data->identifier;

		$environmentDefinition = $repo->findOneByIdentifier($identifier);

		if ($environmentDefinition === null)
		{
			$environmentDefinition = new EnvironmentDefinition();
			$environmentDefinition->setIdentifier($identifier);
		}

		$environmentDefinition->setName($data->name);

		if (isset($data->attributes))
			$environmentDefinition->setAttributes($data->attributes);
		
		$identifiers[] = $environmentDefinition->getIdentifier();

		$this->entityManager->persist($environmentDefinition);
		$this->entityManager->flush();

	}
}
