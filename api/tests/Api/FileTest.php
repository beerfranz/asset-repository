<?php

namespace App\Tests\Api;

use App\Tests\Api\Functional;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileTest extends Functional
{

	public function testFileUpload(): void
	{
		$file = new UploadedFile(__DIR__ . '/../fixtures/image1.png', 'image1.png');

		$client = self::createClient();

		$response = $client->request('POST', '/media_objects', [
		  'headers' => array_merge(['Content-Type' => 'multipart/form-data'],$this->getAdminUser()),
		  'extra' => [
			// If you have additional fields in your MediaObject entity, use the parameters.
			// 'parameters' => [
			// 	// 'title' => 'title'
			// ],
			'files' => [
			  'file' => $file,
			],
		  ]
		]);
		$this->assertResponseIsSuccessful();

		$res_array = json_decode($response->getContent(), true);

    	$this->assertArrayHasKey('contentUrl', $res_array);
		
	}
}
