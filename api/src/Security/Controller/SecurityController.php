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

	#[Route('/groups', name: 'getSecurityGroups', methods: ['GET'])]
	public function getSecurityGroups(Request $request): Response
	{
		return $this->render('@security/groups.html.twig', [ 'navbar' => [ 'security_groups' => 'active' ]]);
	}

	#[Route('/policies', name: 'getSecurityPolicies', methods: ['GET'])]
	public function getSecurityPolicies(Request $request): Response
	{
		return $this->render('@security/policies.html.twig', [ 'navbar' => [ 'security_policies' => 'active' ]]);
	}

	#[Route('/tenants', name: 'getSecurityTenants', methods: ['GET'])]
	public function getSecurityTenants(Request $request): Response
	{
		return $this->render('@security/tenants.html.twig', [ 'navbar' => [ 'security_tenants' => 'active' ]]);
	}
}
