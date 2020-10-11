<?php

/**
 * @file
 * contains FastFrame\Doctrine\Utility\Infrastructure\OrmInfrastructure
 */

namespace FastFrame\Doctrine\Utility\Infrastructure;

use Webfactory\Doctrine\Config\ConnectionConfiguration;
use Webfactory\Doctrine\ORMTestInfrastructure\ORMInfrastructure as TestORMInfrastructure;

/**
 * Overrides the Webfactory Doctrine ORM test infrastructure ORMInfrastructure to use the XML mapping driver
 *
 * @package FastFrame\Doctrine\Utility\Infrastructure
 */
class OrmInfrastructure
	extends TestORMInfrastructure
{
	public static function createWithDependenciesFor(
		$entityClassOrClasses,
		ConnectionConfiguration $connectionConfiguration = null
	) {
		$entityClasses = self::normalizeEntityList($entityClassOrClasses);
		$resolver      = new EntityDependencyResolver($entityClasses);

		return new static($resolver, $connectionConfiguration);
	}

	public function __construct(...$args)
	{
		if (!empty($args)) {
			parent::__construct(...$args);
			$this->configFactory = new ConfigurationFactory;
		}
	}

	protected function removeAnnotationLoaderFromRegistry(?\Closure $loader = null)
	{
	}
}