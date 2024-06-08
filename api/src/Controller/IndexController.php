<?php

namespace App\Controller;

use App\Assets\Repository\AssetRepository;
use App\Assets\Repository\InstanceRepository;

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

}
