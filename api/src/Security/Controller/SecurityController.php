<?php

namespace App\Security\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class SecurityController extends AbstractController
{

	#[Route('/users', name: 'getSecurityUsers', methods: ['GET'])]
	public function getSecurityUsers(Request $request): Response
	{
		return $this->render('@security/users.html.twig', [ 'navbar' => [ 'security_users' => 'active' ]]);
	}

}
