<?php

namespace Beerfranz\RogerBundle;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class BeerfranzRogerBundle extends AbstractBundle
{
	public function getPath(): string
	{
		return dirname(__DIR__);
	}

	public function loadExtension(array $config, ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder): void
	{
		// load an XML, PHP or Yaml file
		$containerConfigurator->import('../config/services.yaml');
	}
}