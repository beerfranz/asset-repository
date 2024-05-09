<?php

namespace App\Tests\Api;

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
	}

	protected function testGet(string $uri, array $context) {
		$this->response = static::createClient()->request('GET', $uri,
      [ 'headers' => $context['headers'] ]);

    $this->assertResponseStatusCodeSame(200);
    $this->assertJsonContains($context['output']);
	}

	protected function testDelete(string $uri, array $context) {
		$this->response = static::createClient()->request('DELETE', $uri,
      [ 'headers' => $context['headers']
    ]);
    $this->assertResponseStatusCodeSame(204);

    $this->assertNotExists($uri, $context);

    if (isset($context['withAudit']) && $context['withAudit'] === true) {
    	$context['auditAction'] = 'remove';
    	$this->testAudit($context);
    }
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
	}

	protected function testIdempotentCrud(string $uri, array $context) {
		$this->assertNotExists($uri, $context);
		$this->testPut($uri, array_merge($context, ['withGetTest' => true ]));
		$this->testDelete($uri, $context);
	}

	protected function calculateSimpleOutput(string $class, string $identifier, string $uri, array $input = []): array
	{
		return array_merge($input, [
	      '@context' => '/contexts/'. $class,
	      '@id' => $uri,
	      '@type' => $class,
	      'identifier' => $identifier,
	    ]);
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
