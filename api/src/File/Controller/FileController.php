<?php

namespace App\File\Controller;

use App\File\Entity\MediaObject;
use App\File\Repository\MediaObjectRepository;

use Vich\UploaderBundle\Handler\DownloadHandler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FileController extends AbstractController
{

	#[Route('/uploads/{tenant}/{file}', name: 'getFile', methods: ['GET'])]
	public function getFile(MediaObjectRepository $repo, DownloadHandler $downloadHandler, string $file, string $tenant): Response
	{
		$object = $repo->findOneBy([ 'tenant' => $tenant, 'filePath' => $file ]);

		if ($object === null)
			throw $this->createNotFoundException();

		return $downloadHandler->downloadObject($object, $fileField = 'file', MediaObject::class, $fileName = true, $forceDownload = false);
	}
}
