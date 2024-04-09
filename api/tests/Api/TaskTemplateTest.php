<?php

namespace App\Tests\Api;

use App\Tests\Api\Functional;
use App\Tests\Factory\TaskTemplateFactory;

class TaskTemplateTest extends Functional
{

  public function testAdminCreateTaskTemplate(): void
  {
    $userHeaders = $this->getAdminUser();

    $identifier = 'tt-001';
    $input = [
      'title' => 'test title',
      'description' => 'test description',
    ];

    $output = array_merge($input, [
      '@context' => '/contexts/TaskTemplate',
      '@id' => '/task_templates/' . $identifier,
      '@type' => 'TaskTemplate',
      'identifier' => $identifier,
    ]);

    static::createClient()->request('GET', '/task_templates/' . $identifier,
      [ 'headers' => $userHeaders ]);
    $this->assertResponseStatusCodeSame(404);

    static::createClient()->request('PUT', '/task_templates/' . $identifier , [
      'json' => $input,
      'headers' => $userHeaders
    ]);

    $this->assertResponseStatusCodeSame(200);
    $this->assertJsonContains($output);

    // Read task
    static::createClient()->request('GET', '/task_templates/' . $identifier,
      [ 'headers' => $userHeaders ]);

    $this->assertResponseStatusCodeSame(200);
    $this->assertJsonContains($output);

    // Delete task
    static::createClient()->request('DELETE', '/task_templates/' . $identifier,
      [ 'headers' => $userHeaders ]);
    $this->assertResponseStatusCodeSame(204);

    static::createClient()->request('GET', '/task_templates/' . $identifier,
      [ 'headers' => $userHeaders ]);
    $this->assertResponseStatusCodeSame(404);

  }

  public function testAdminCreateTaskTemplateAndPropagate(): void
  {
    $userHeaders = $this->getAdminUser();

    $identifier = 'tt-001';
    $input = [
      'title' => 'test title',
      'description' => 'test description',
    ];

    $output = array_merge($input, [
      '@context' => '/contexts/TaskTemplate',
      '@id' => '/task_templates/' . $identifier,
      '@type' => 'TaskTemplate',
      'identifier' => $identifier,
    ]);

    $taskIdentifier = $identifier . '_' . 't-001';
    $taskOutput = array_merge($input, [
      '@context' => '/contexts/Task',
      '@id' => '/tasks/' . $taskIdentifier,
      '@type' => 'Task',
      'identifier' => $taskIdentifier,
    ]);

    static::createClient()->request('PUT', '/task_templates/' . $identifier , [
      'json' => $input,
      'headers' => $userHeaders
    ]);

    $this->assertResponseStatusCodeSame(200);
    $this->assertJsonContains($output);

    static::createClient()->request('PUT', '/task_templates/' . $identifier . '/generate/t-001', [
      'json' => [], 
      'headers' => $userHeaders
    ]);

    $this->assertResponseStatusCodeSame(200);

    // Read task
    static::createClient()->request('GET', '/tasks/' . $taskIdentifier,
      [ 'headers' => $userHeaders ]
    );
    $this->assertResponseStatusCodeSame(200);
    $this->assertJsonContains($taskOutput);
  }

}
