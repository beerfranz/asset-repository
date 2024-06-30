<?php

namespace App\Indicators\Controller;

use App\Indicators\Repository\IndicatorRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class IndicatorController extends AbstractController
{

	#[Route('/ui/indicators', name: 'getIndicators', methods: ['GET'])]
	public function getIndicators(Request $request): Response
	{
		return $this->render('@indicator/indicators.html.twig', [ 'navbar' => [ 'indicators' => 'active' ] ]);
	}

	#[Route('/ui/indicators/{identifier}', name: 'getIndicator', methods: ['GET'])]
	public function getIndicator(string $identifier, IndicatorRepository $repo, Request $request): Response
	{
		$indicator = $repo->findOneByIdentifier($identifier);

		return $this->render('@indicator/indicator.html.twig', [ 'indicator' => $indicator ]);
	}

}
