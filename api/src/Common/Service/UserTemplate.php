<?php

namespace App\Common\Service;

use App\Common\Entity\UserTemplateType;

use Twig\Sandbox\SecurityPolicy;
use Twig\Extension\SandboxExtension;
use Twig\Loader\ArrayLoader;
use Twig\Loader\ChainLoader;
use Twig\Environment;

use Psr\Log\LoggerInterface;

class UserTemplate
{
  protected $logger;

  public function __construct(
    LoggerInterface $logger,
  ) {
    $this->logger = $logger;
  }

  public function template(string $userInput, array $templateVariables = []): UserTemplateType
  {
    // Content of user input
    $userLoader = new ArrayLoader([
      'userInput.html.twig' => $userInput,
    ]);

    $sandboxLoader = new ArrayLoader([
      'sandbox.html.twig' => '{% sandbox %}{% include "userInput.html.twig" %}{% endsandbox %}',
    ]);

    $loader = new ChainLoader([ $userLoader, $sandboxLoader ]);

    $twig = new Environment($loader);
    $twig->addExtension($this->getTwigSandbox());
    try {
      $output = $twig->render('sandbox.html.twig', $templateVariables);
      return new UserTemplateType($output);
    } catch(\Exception $e) {
      $this->logger->warning('Invalid user template: ' . $userInput . '. ' . $e->getMessage());
      return new UserTemplateType(null, $e->getMessage());
    }
  }

  public function test(string $userInput, array $templateVariables = []): UserTemplateType
  {
    return $this->template('{{ ' . $userInput . ' ? "true" : "false" }}', $templateVariables);
  }

  protected function getTwigSandbox(): SandboxExtension
  {
    return new SandboxExtension($this->getTwigPolicy());
  }

  protected function getTwigPolicy(): SecurityPolicy
  {
    $tags = [
      'if',
    ];

    $filters = [
      'upper',
      'lower',
      'escape',
    ];

    $methods = [];

    $properties = [];

    $functions = [
      'max',
      'min',
    ];

    $policy = new SecurityPolicy($tags, $filters, $methods, $properties, $functions);

    return $policy;
  }
}