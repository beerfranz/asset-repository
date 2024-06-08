<?php

namespace App\Controller;

use App\Repository\AssetRepository;
use App\Repository\AssetDefinitionRepository;
use App\Repository\AssetAuditRepository;
use App\Repository\InstanceRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class IndexController extends AbstractController
{

  #[Route('/ui/whoami', name: 'getWhoami', methods: ['GET'])]
  public function getWhoami(Request $request): JsonResponse
  {
    var_dump($this->getUser()); exit;
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

  #[Route('/ui/admin/settings', name: 'getSettingss', methods: ['GET'])]
  public function getSettingss(Request $request): Response
  {
    return $this->render('settings.html.twig', [ 'navbar' => [ 'settings' => 'active' ] ]);
  }
}
