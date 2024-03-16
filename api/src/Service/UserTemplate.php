<?php

namespace App\Service;

// use App\Entity\Asset;
// use App\Entity\Instance;

// use Doctrine\ORM\EntityManagerInterface;

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

  public function template(string $userInput, array $templateVariables = [])
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
      return $output;
    } catch(\Exception $e) {
      $this->logger->warning('Invalid user template: ' . $userInput . '. ' . $e->getMessage());
      return $e->getMessage();
    }
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