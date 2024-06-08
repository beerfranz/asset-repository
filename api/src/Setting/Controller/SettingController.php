<?php

namespace App\Setting\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SettingController extends AbstractController
{

	#[Route('/ui/admin/settings', name: 'getSettingss', methods: ['GET'])]
	public function getSettingss(Request $request): Response
	{
		return $this->render('@setting/settings.html.twig', [ 'navbar' => [ 'settings' => 'active' ] ]);
	}
}
