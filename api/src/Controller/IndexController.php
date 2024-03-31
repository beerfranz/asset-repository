<?php

namespace App\Controller;

use App\Repository\AssetRepository;
use App\Repository\AssetDefinitionRepository;
use App\Repository\AssetAuditRepository;
use App\Repository\InstanceRepository;
use App\Repository\RiskManagerRepository;
use App\Repository\RiskRepository;
use App\Repository\IndicatorRepository;

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
  public function getAsset(string $identifier, AssetRepository $repo, Request $request, AssetAuditRepository $auditRepo): Response
  {
    $asset = $repo->findOneByIdentifier($identifier);
    $assetAudits = $auditRepo->findBy([ 'subject' => $identifier ], [ 'datetime' => 'DESC' ]);

    return $this->render('asset.html.twig', [ 'asset' => $asset, 'assetAudits' => $assetAudits ]);
  }

  #[Route('/ui/instances/{identifier}', name: 'getInstance', methods: ['GET'])]
  public function getInstance(string $identifier, InstanceRepository $repo, Request $request): Response
  {
    $instance = $repo->findOneByIdentifier($identifier);

    return $this->render('instance.html.twig', [ 'instance' => $instance, 'navbar' => [ 'instances' => 'active' ] ]);
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

  #[Route('/ui/asset_definitions/{identifier}', name: 'getAssetDefinition', methods: ['GET'])]
  public function getAssetDefinition(string $identifier, AssetDefinitionRepository $repo, Request $request): Response
  {
    $assetDefinition = $repo->findOneByIdentifier($identifier);

    return $this->render('asset-definition.html.twig', [ 'assetDefinition' => $assetDefinition ]);
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

  #[Route('/ui/environments', name: 'getEnvironments', methods: ['GET'])]
  public function getEnvironments(Request $request): Response
  {
    return $this->render('environments.html.twig', [ 'navbar' => [ 'environments' => 'active' ] ]);
  }

  #[Route('/ui/map', name: 'getMap', methods: ['GET'])]
  public function getMap(Request $request): Response
  {
    return $this->render('map.html.twig', [ 'navbar' => [ 'map' => 'active' ] ]);
  }

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

  #[Route('/ui/dashboard', name: 'getDashboard', methods: ['GET'])]
  public function getDashboard(Request $request, AssetRepository $assetRepo, InstanceRepository $instanceRepo): Response
  {
    $countAssets = $assetRepo->countAssets();
    $countAssetsReconcilied = $assetRepo->countAssetsReconcilied();
    $countInstances = $instanceRepo->countInstances();
    $countInstancesValidated = $instanceRepo->countInstancesValidated();
    $countInstancesTotalChecks = $instanceRepo->countInstancesTotalChecks();
    $countInstancesTotalErrors = $instanceRepo->countInstancesTotalErrors();
    $countInstancesReconcilied = $instanceRepo->countInstancesReconcilied();

    return $this->render('dashboard.html.twig', [ 'navbar' => [ 'dashboard' => 'active' ], 'stats' => [
      'countAssets' => $countAssets,
      'countAssetsReconcilied' => $countAssetsReconcilied,
      'countInstances' => $countInstances,
      'countInstancesValidated' => $countInstancesValidated,
      'countInstancesTotalChecks' => $countInstancesTotalChecks,
      'countInstancesTotalErrors' => $countInstancesTotalErrors,
      'countInstancesReconcilied' => $countInstancesReconcilied,
    ] ]);
  }

  #[Route('/ui/tasks', name: 'getTasks', methods: ['GET'])]
  public function getTasks(Request $request): Response
  {
    return $this->render('tasks.html.twig', [ 'navbar' => [ 'tasks' => 'active' ] ]);
  }

  #[Route('/ui/task-templates', name: 'getTaskTemplates', methods: ['GET'])]
  public function getTaskTemplates(Request $request): Response
  {
    return $this->render('task-templates.html.twig', [ 'navbar' => [ 'task_templates' => 'active' ] ]);
  }

  #[Route('/ui/indicators', name: 'getIndicators', methods: ['GET'])]
  public function getIndicators(Request $request): Response
  {
    return $this->render('indicators.html.twig', [ 'navbar' => [ 'indicators' => 'active' ] ]);
  }

  #[Route('/ui/indicators/{identifier}', name: 'getIndicator', methods: ['GET'])]
  public function getIndicator(string $identifier, IndicatorRepository $repo, Request $request): Response
  {
    $indicator = $repo->findOneByIdentifier($identifier);

    return $this->render('indicator.html.twig', [ 'indicator' => $indicator ]);
  }
}
