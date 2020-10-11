<?php

/**
 * @file
 * contains FastFrame\Doctrine\Utility\Infrastructure\EntityDependencyResolver
 */

namespace FastFrame\Doctrine\Utility\Infrastructure;

/**
 * Overrides the Webfactory Doctrine ORM test infrastructure EntityDependencyResolver to use the XML driver
 *
 * @package FastFrame\Doctrine\Utility\Infrastructure
 */
class EntityDependencyResolver
	extends \Webfactory\Doctrine\ORMTestInfrastructure\EntityDependencyResolver
{
	public function __construct(array $entityClasses)
	{
		parent::__construct($entityClasses);
		$this->configFactory = new ConfigurationFactory;
	}
}