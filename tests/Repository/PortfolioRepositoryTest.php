<?php

declare(strict_types = 1);

namespace App\Tests\Repository;

use App\Entity\Portfolio;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PortfolioRepositoryTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testFindUnallocatedPortfolios()
    {
        $portfolios = $this->entityManager
            ->getRepository(Portfolio::class)
            ->findUnallocatedPortfolios()
        ;

        $this->assertCount(1, $portfolios);
        $this->assertEquals('Stocks', $portfolios[0]->getName());
    }

    public function testFindUnallocatedPortfoliosExcluding()
    {
        $repository = $this->entityManager->getRepository(Portfolio::class);
        $entity = $repository->findOneByUnallocated(true);
        $portfolios = $repository->findUnallocatedPortfolios($entity);

        $this->assertCount(0, $portfolios);

        $newEntity = new Portfolio();
        $newEntity->setName('Another Portfolio')->setUnallocated(true);
        $this->entityManager->persist($newEntity);
        $this->entityManager->flush();

        $portfolios = $repository->findUnallocatedPortfolios($entity);
        $this->assertCount(1, $portfolios);
        $this->assertEquals('Another Portfolio', $portfolios[0]->getName());
    }

    public function testGetPercentAllocated()
    {
        $result = $this->entityManager
            ->getRepository(Portfolio::class)
            ->getPercentAllocated()
        ;

        $this->assertEquals(0.05, $result);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
