<?php

namespace Beerfranz\RogerBundle\Tests;

trait RogerTestApiTrait {

	protected function testGetCollection(string $uri, array $context)
	{
		$this->testGet($uri, $context);
	}

	protected function testPost(string $uri, array $context)
	{
		$this->response = static::createClient()->request('POST', $uri,
			[
				'json' => $context['input'],
				'headers' => $context['headers'],
			]
		);
		$this->assertResponseStatusCodeSame(201);
		$this->assertJsonContains($context['output']);

		return $this->response;
	}

	protected function testGet(string $uri, array $context) {
		$this->response = static::createClient()->request('GET', $uri,
			[ 'headers' => $context['headers'] ]);

		$this->assertResponseStatusCodeSame(200);
		$this->assertJsonContains($context['output']);

		return $this->response;
	}

	protected function testDelete(string $uri, array $context) {
		$this->response = static::createClient()->request('DELETE', $uri,
				[ 'headers' => $context['headers'] ]
		);
		$this->assertResponseStatusCodeSame(204);

		$this->assertNotExists($uri, $context);

		if (isset($context['withAudit']) && $context['withAudit'] === true) {
			$context['auditAction'] = 'remove';
			$this->testAudit($context);
		}

		return $this->response;
	}

	protected function testPut(string $uri, array $context) {
		$this->response = static::createClient()->request('PUT', $uri,
			[
			'json' => $context['input'],
			'headers' => $context['headers'],
			]
		);
		$this->assertResponseStatusCodeSame(200);
		$this->assertJsonContains($context['output']);

		if (isset($context['withGetTest']) && $context['withGetTest'] === true)
			$this->testGet($uri, $context);

		if (isset($context['withAudit']) && $context['withAudit'] === true) {
			if (!isset($context['auditAction']))
				$context['auditAction'] = 'create';
			$this->testAudit($context);
		}

		return $this->response;
	}

	protected function testPatch(string $uri, array $context) {
		$this->response = static::createClient()->request('PATCH', $uri,
			[
				'json' => $context['input'],
				'headers' => array_merge(['Content-Type' => 'application/merge-patch+json'], $context['headers']),
			]
		);

		$this->assertResponseStatusCodeSame($this->getContextResponseStatus($context));
		$this->assertJsonContains($context['output']);

		if (isset($context['withGetTest']) && $context['withGetTest'] === true)
			$this->testGet($uri, $context);

		return $this->response;
	}

	protected function testIdempotentCrud(string $uri, array $context) {
		$this->assertNotExists($uri, $context);
		$this->testPut($uri, array_merge($context, ['withGetTest' => true ]));
		$this->testDelete($uri, $context);
	}

	protected function testCrud(string $uri, array $context) {
		$response = $this->testPost($uri, $context);
		$data = json_decode($response->getContent(), true);
		$uri = $data['@id'];
		$this->testGet($uri, $context);
		$this->testDelete($uri, $context);
	}

	protected function calculateSimpleOutput(string $class, ?string $identifier, ?string $uri, ?array $input = []): array
	{
		$out = [
			'@context' => '/contexts/'. $class,
			'@type' => $class,
		];

		if (is_array($input))
			$out = array_merge($input, $out);

		if ($uri !== null)
			$out['@id'] = $uri;

		if ($identifier !== null)
			$out['identifier'] = $identifier;

		return $out;
	}

	protected function getContextResponseStatus(array $context) {
		if (isset($context['responseStatus']))
			return $context['responseStatus'];
		else
			return 200;
	}

	protected function testAudit(array $context) {
		$this->processQueue();
		
		$auditContext = $context;
		$auditContext['input'] = null;
		$subjectKind = $context['output']['@type'];
		$subject = $context['output']['identifier'];
		$url = '/audits/subject-kinds/' . $subjectKind . '/subjects/' . $subject;
		$data = [
			'subjectKind' => $subjectKind,
			'subject' => $subject,
			'actor' => '1',
			'action' => $context['auditAction'],
		];

		$auditContext['output'] = [
			'@context' => '/contexts/Audit',
			'@id' => $url,
			'@type' => 'hydra:Collection',
			'hydra:member' => [ $data ],
		];
		$this->testGet(
			$url,
			$auditContext,
		);
	}

}
