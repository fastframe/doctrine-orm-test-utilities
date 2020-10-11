<?php

/**
 * @file
 * contains FastFrame\Doctrine\Utility\TestCase
 */

namespace FastFrame\Doctrine\Utility;

use Acelaya\Doctrine\Type\PhpEnumType;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\Setup;
use PHPUnit\Framework\TestCase as UnitTestCase;

/**
 * Utilities to aid in the testing Doctrine entities & repositories
 *
 * @package FastFrame\Doctrine\Utility
 */
abstract class TestCase
	extends UnitTestCase
{
	/**
	 *   name => [doctrine_type, class]
	 *
	 * @var array List of the Doctrine types to register
	 */
	protected $doctrineTypeMap = [];

	/**
	 * @var array List of the Doctrine ENUM types to register
	 */
	protected $doctrineEnumMap = [];

	/**
	 * @var EntityManager
	 */
	protected $entityManager;

	/**
	 * @var string Path to the entities that are being tested
	 */
	protected $entityPath;

	public static function doctrineMetadataPath()
	{
		if (!defined('DOCTRINE_METADATA_PATH')) {
			throw new \RuntimeException("Please define DOCTRINE_METADATA_PATH to your entity definitions");
		}

		return DOCTRINE_METADATA_PATH;
	}

	/**
	 * Creates a mocked repository
	 *
	 * @param string $repositoryName
	 * @param string $className
	 *
	 * @return mixed
	 */
	protected function loadRepositoryFromEntityManagerMock($repositoryName, $className)
	{
		$metadata = $this->createMock(ClassMetadata::class);
		$metadata->method('getTableName')->willReturn('{tableName}');

		$mock = $this->createMock(EntityManager::class);
		$mock->method('getClassMetadata')->willReturn((object)['name' => $className]);
		$mock->method('persist')->willReturn(null);
		$mock->method('flush')->willReturn(null);

		return new $repositoryName($mock, $metadata);
	}

	/**
	 * Builds a full entity Manager
	 *
	 * ENV vars can be set to change the database connection in use
	 *
	 * @return EntityManager
	 * @throws \Doctrine\ORM\ORMException
	 */
	protected function buildEntityManager(): EntityManager
	{
		$this->entityPath = static::doctrineMetadataPath();

		return $this->entityManager = EntityManager::create(
			[
				'driver'   => $ENV['DB_DRIVER'] ?? 'pdo_mysql',
				'user'     => $ENV['DB_USER'] ?? 'devuser',
				'password' => $ENV['DB_PASS'] ?? 'password',
				'host'     => $ENV['DB_HOST'] ?? 'localhost',
				'dbname'   => $ENV['DB_NAME'] ?? 'dev'
			],
			Setup::createXMLMetadataConfiguration([$this->entityPath], true)
		);
	}

	/**
	 * @return EntityManager The existing EntityManager or a new one
	 */
	protected function getEntityManager(): EntityManager
	{
		return $this->entityManager ?? ($this->entityManager = $this->buildEntityManager());
	}

	/**
	 * Registers the Types defined in doctrineTypeMap
	 *
	 * @param EntityManager $em
	 *
	 * @throws \Doctrine\DBAL\DBALException
	 */
	protected function registerDoctrineTypes(EntityManager $em)
	{
		foreach ($this->doctrineTypeMap as $name => $def) {
			Type::addType($name, $def[1]);
		}

		$platform = $em->getConnection()->getDatabasePlatform();
		foreach ($this->doctrineEnumMap as $name => $def) {
			$platform->registerDoctrineTypeMapping($def[0], $name);
		}
	}

	/**
	 * Registers the ENUM's in the doctrineEnumMap
	 *
	 * The ENUM's are based on myclabs/php-enum & acelaya/doctrine-enum-type
	 *
	 * @param EntityManager $em
	 *
	 * @throws \Doctrine\DBAL\DBALException
	 */
	protected function registerDoctrineEnumTypes(EntityManager $em)
	{
		if (!class_exists(PhpEnumType::class) || empty($this->doctrineEnumMap)) {
			return;
		}

		PhpEnumType::registerEnumTypes($this->doctrineEnumMap);

		$platform = $em->getConnection()->getDatabasePlatform();
		foreach ($this->doctrineEnumMap as $name => $class) {
			$platform->registerDoctrineTypeMapping('VARCHAR', $name);
		}
	}
}