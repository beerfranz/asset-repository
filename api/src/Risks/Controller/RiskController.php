<?php

namespace App\Risks\Controller;

use App\Risks\Repository\RiskManagerRepository;
use App\Risks\Repository\RiskRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class RiskController extends AbstractController
{

	#[Route('/ui/risk-managers', name: 'getRiskManagers', methods: ['GET'])]
	public function getRiskManagers(Request $request): Response
	{
		return $this->render('risk-managers.html.twig', [ 'navbar' => [ 'riskManagers' => 'active' ] ]);
	}

	#[Route('/ui/risk-managers/{identifier}', name: 'getRiskManager', methods: ['GET'])]
	public function getRiskManager(string $identifier, RiskManagerRepository $repo, RiskRepository $riskRepo, Request $request): Response
	{
		$riskManager = $repo->findOneByIdentifier($identifier);

		$risks = $riskRepo->findBy([ 'riskManager' => $riskManager]);

		return $this->render('risk-manager.html.twig', [ 'riskManager' => $riskManager, 'risks' => $risks ]);
	}

}
