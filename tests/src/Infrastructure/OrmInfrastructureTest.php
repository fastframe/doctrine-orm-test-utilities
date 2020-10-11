<?php

namespace FastFrame\Doctrine\Utility\Infrastructure;

use Doctrine\ORM\EntityManager;
use FastFrame\Doctrine\Entity;
use PHPUnit\Framework\TestCase;

/**
 * Tests that the ORM infrastructure utilizes the XML driver, as the entities have no annotations
 *
 * @package FastFrame\Doctrine\Utility\Infrastructure
 */
class OrmInfrastructureTest
	extends TestCase
{
	protected $infrastructure;

	protected function setUp(): void
	{
		parent::setUp();
		$this->infrastructure = new OrmInfrastructure([Entity\Tester::class]);
	}

	protected function tearDown(): void
	{
		$this->infrastructure = null;
	}

	public function testGetEntityManagerReturnsDoctrineEntityManagers()
	{
		$entityManager = $this->infrastructure->getEntityManager();

		$this->assertInstanceOf(EntityManager::class, $entityManager);
	}

	public function testGetRepositoryReturnsRepositoryThatBelongsToEntityClass()
	{
		$repository = $this->infrastructure->getRepository(Entity\Tester::class);

		$this->assertInstanceOf(Entity\Testers::class, $repository);
	}

	public function testCreateWithDependenciesForCreatesInfrastructureForSetOfEntities()
	{
		$infrastructure = OrmInfrastructure::createWithDependenciesFor([Entity\Tester::class, Entity\Referenced::class]);

		$this->assertInstanceOf(ORMInfrastructure::class, $infrastructure);
	}
}