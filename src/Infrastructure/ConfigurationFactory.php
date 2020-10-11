<?php

/**
 * @file
 * contains FastFrame\Doctrine\Utility\Infrastructure\ConfigurationFactory
 */

namespace FastFrame\Doctrine\Utility\Infrastructure;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Doctrine\ORM\Tools\Setup;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use FastFrame\Doctrine\Utility\DoctrineTestCase;
use Webfactory\Doctrine\ORMTestInfrastructure\ConfigurationFactory as TestInfrastructureConfigurationFactory;
use Webfactory\Doctrine\ORMTestInfrastructure\EntityListDriverDecorator;

/**
 * Overrides the Webfactory Doctrine ORM test infrastructures ConfigurationFactory to make it use an XML driver
 *
 * @package FastFrame\Doctrine\Utility\Infrastructure
 */
class ConfigurationFactory
	extends TestInfrastructureConfigurationFactory
{
	/**
	 * @var MappingDriver
	 */
	public static $driver;

	public function createFor(array $entityClasses)
	{
		$config = Setup::createConfiguration(true, null, new ArrayCache());
		$config->setMetadataDriverImpl(new EntityListDriverDecorator($this->resolvePrimaryDriver(), $entityClasses));

		return $config;
	}

	/**
	 * Resolves the primary mapping driver to either the first one or a new one.
	 *
	 * @return MappingDriver
	 */
	protected function resolvePrimaryDriver(): MappingDriver
	{
		return static::$driver ?? (static::$driver = new XmlDriver(DoctrineTestCase::doctrineMetadataPath()));
	}
}