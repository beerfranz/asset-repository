<?php

namespace App\Assets\Controller;

use App\Assets\Repository\AssetRepository;
use App\Assets\Repository\AssetDefinitionRepository;
use App\Assets\Repository\AssetAuditRepository;
use App\Assets\Repository\InstanceRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AssetController extends AbstractController
{
	#[Route('/ui', name: 'getIndex', methods: ['GET'])]
	public function getIndex(Request $request): Response
	{
		return $this->render('@asset/assets.html.twig', [ 'navbar' => [ 'assets' => 'active' ]]);
	}

	#[Route('/ui/assets', name: 'getAssets', methods: ['GET'])]
	public function getAssets(Request $request): Response
	{
		return $this->render('@asset/assets.html.twig', [ 'navbar' => [ 'assets' => 'active' ]]);
	}

	#[Route('/ui/assets/{identifier}', name: 'getAsset', methods: ['GET'])]
	public function getAsset(string $identifier, AssetRepository $repo, Request $request, AssetAuditRepository $auditRepo): Response
	{
		$asset = $repo->findOneByIdentifier($identifier);
		$assetAudits = $auditRepo->findBy([ 'subject' => $identifier ], [ 'datetime' => 'DESC' ]);

		return $this->render('@asset/asset.html.twig', [ 'asset' => $asset, 'assetAudits' => $assetAudits ]);
	}

	#[Route('/ui/instances/{identifier}', name: 'getInstance', methods: ['GET'])]
	public function getInstance(string $identifier, InstanceRepository $repo, Request $request): Response
	{
		$instance = $repo->findOneByIdentifier($identifier);

		return $this->render('@asset/instance.html.twig', [ 'instance' => $instance, 'navbar' => [ 'instances' => 'active' ] ]);
	}
	#[Route('/ui/instances', name: 'getInstances', methods: ['GET'])]
	public function getInstances(Request $request): Response
	{
		return $this->render('@asset/instances.html.twig', [ 'navbar' => [ 'instances' => 'active' ] ]);
	}

	#[Route('/ui/asset_definitions', name: 'getAssetDefinitions', methods: ['GET'])]
	public function getAssetDefinitions(Request $request): Response
	{
		return $this->render('asset-definitions.html.twig', [ 'navbar' => [ 'asset_definitions' => 'active' ] ]);
	}

	#[Route('/ui/asset_definitions/{identifier}', name: 'getAssetDefinition', methods: ['GET'])]
	public function getAssetDefinition(string $identifier, AssetDefinitionRepository $repo, Request $request): Response
	{
		$assetDefinition = $repo->findOneByIdentifier($identifier);

		return $this->render('asset-definition.html.twig', [ 'assetDefinition' => $assetDefinition ]);
	}

	#[Route('/ui/sources', name: 'getSources', methods: ['GET'])]
	public function getSources(Request $request): Response
	{
		return $this->render('@asset/sources.html.twig', [ 'navbar' => [ 'sources' => 'active' ] ]);
	}

	#[Route('/ui/kinds', name: 'getKinds', methods: ['GET'])]
	public function getKinds(Request $request): Response
	{
		return $this->render('@asset/kinds.html.twig', [ 'navbar' => [ 'kinds' => 'active' ] ]);
	}

	#[Route('/ui/map', name: 'getMap', methods: ['GET'])]
	public function getMap(Request $request): Response
	{
		return $this->render('map.html.twig', [ 'navbar' => [ 'map' => 'active' ] ]);
	}

	#[Route('/ui/environment_definitions', name: 'getEnvironmentDefinitions', methods: ['GET'])]
	public function getEnvironmentDefinitions(Request $request): Response
	{
		return $this->render('environment-definitions.html.twig', [ 'navbar' => [ 'environment_definitions' => 'active' ] ]);
	}

	#[Route('/ui/audits', name: 'getAudits', methods: ['GET'])]
	public function getAudits(Request $request): Response
	{
		return $this->render('audits.html.twig', [ 'navbar' => [ 'audits' => 'active' ] ]);
	}

	#[Route('/ui/versions', name: 'getVersions', methods: ['GET'])]
	public function getVersions(Request $request): Response
	{
		return $this->render('versions.html.twig', [ 'navbar' => [ 'versions' => 'active' ] ]);
	}

	#[Route('/ui/environments', name: 'getEnvironments', methods: ['GET'])]
	public function getEnvironments(Request $request): Response
	{
		return $this->render('environments.html.twig', [ 'navbar' => [ 'environments' => 'active' ] ]);
	}
}
