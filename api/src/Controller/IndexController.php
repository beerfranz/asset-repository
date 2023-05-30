<?php

namespace App\Controller;

use App\Repository\AssetRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class IndexController extends AbstractController
{

  #[Route('/ui', name: 'getIndex', methods: ['GET'])]
  public function getIndex(Request $request): Response
  {
    return $this->render('assets.html.twig', [ 'navbar' => [ 'assets' => 'active' ]]);
  }

  #[Route('/ui/assets', name: 'getAssets', methods: ['GET'])]
  public function getAssets(Request $request): Response
  {
    return $this->render('assets.html.twig', [ 'navbar' => [ 'assets' => 'active' ]]);
  }

  #[Route('/ui/whoami', name: 'getWhoami', methods: ['GET'])]
  public function getWhoami(Request $request): JsonResponse
  {
    var_dump($this->getUser()); exit;
  }

  #[Route('/ui/assets/{identifier}', name: 'getAsset', methods: ['GET'])]
  public function getAsset(string $identifier, AssetRepository $repo, Request $request): Response
  {
    $asset = $repo->findOneByIdentifier($identifier);

    return $this->render('asset.html.twig', [ 'asset' => $asset ]);
  }

  #[Route('/ui/instances/{identifier}', name: 'getInstance', methods: ['GET'])]
  public function getInstance(string $identifier, AssetRepository $repo, Request $request): Response
  {
    $asset = $repo->findOneByIdentifier($identifier);

    return $this->render('instance.html.twig', [ 'asset' => $asset, 'navbar' => [ 'instances' => 'active' ] ]);
  }
  #[Route('/ui/instances', name: 'getInstances', methods: ['GET'])]
  public function getInstances(Request $request): Response
  {
    return $this->render('instances.html.twig', [ 'navbar' => [ 'instances' => 'active' ] ]);
  }

  #[Route('/ui/asset_definitions', name: 'getAssetDefinitions', methods: ['GET'])]
  public function getAssetDefinitions(Request $request): Response
  {
    return $this->render('asset-definitions.html.twig', [ 'navbar' => [ 'asset_definitions' => 'active' ] ]);
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

  #[Route('/ui/sources', name: 'getSources', methods: ['GET'])]
  public function getSources(Request $request): Response
  {
    return $this->render('sources.html.twig', [ 'navbar' => [ 'sources' => 'active' ] ]);
  }

  #[Route('/ui/kinds', name: 'getKinds', methods: ['GET'])]
  public function getKinds(Request $request): Response
  {
    return $this->render('kinds.html.twig', [ 'navbar' => [ 'kinds' => 'active' ] ]);
  }

  #[Route('/ui/map', name: 'getMap', methods: ['GET'])]
  public function getMap(Request $request): Response
  {
    return $this->render('map.html.twig', [ 'navbar' => [ 'map' => 'active' ] ]);
  }
}
