<?php

namespace App\Security\ControllerPublic;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class PublicController extends AbstractController
{

	#[Route('navigation.json', name: 'getNavigation', methods: ['GET'])]
	public function getNavigation(Request $request): JsonResponse
	{
		$nav = [];

		$asset = [
			'title' => 'Assets',
		];

		$indicator = [
			'title' => 'Indicators',
		];


		$nav['asset'] = $asset;
		$nav['indicator'] = $indicator;

		if ($this->isGranted('ASSESSMENT_READ')) {
			$nav['assessment'] = true;
		}

		return new JsonResponse($nav);
	}
}
