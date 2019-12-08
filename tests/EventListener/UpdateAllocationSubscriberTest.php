<?php

declare(strict_types = 1);

namespace App\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use App\Entity\Portfolio;
use App\Repository\PortfolioRepository;
use App\EventListener\UpdateAllocationSubscriber;

class UpdateAllocationSubscriberTest extends TestCase
{
    private $repository;
    private $args;
    private $subscriber;

    protected function setup()
    {
        $this->repository = $this->createMock(PortfolioRepository::class);

        $this->manager = $this->createMock(EntityManagerInterface::class);
        $this->manager->expects($this->any())
                ->method('getRepository')
                ->willReturn($this->repository)
            ;

        $this->subscriber = new UpdateAllocationSubscriber();
    }

    public function testSubscribedToPrePersist()
    {
        $this->assertContains('prePersist', $this->subscriber->getSubscribedEvents());
    }

    public function testPrePersistOnlyOnPortfolioEntity()
    {
        $entity = new \stdClass;
        $this->manager->expects($this->never())->method('getRepository');
        $args = new LifecycleEventArgs($entity, $this->manager);
        $this->subscriber->prePersist($args);
    }

    public function testPrePersist()
    {
        $entity = (new Portfolio)->setUnallocated(true);

        $portfolios = [
            $this->createUnallocatedPortfolio(),
        ];

        $this->expectsAllocation('0.0500');
        $this->returnsPortfolios($portfolios);
        $args = new LifecycleEventArgs($entity, $this->manager);

        $this->subscriber->prePersist($args);

        $this->assertEquals('0.4750', $entity->getAllocationPercent());
        $this->assertEquals('0.4750', $portfolios[0]->getAllocationPercent());
    }

    public function testPrePersistWithMultiplePortfolios()
    {
        $entity = (new Portfolio)->setUnallocated(true);

        $portfolios = [
            $this->createUnallocatedPortfolio(),
            $this->createUnallocatedPortfolio(),
        ];

        $this->expectsAllocation('0.0500');
        $this->returnsPortfolios($portfolios);
        $args = new LifecycleEventArgs($entity, $this->manager);

        $this->subscriber->prePersist($args);

        $portfolios[] = $entity;

        $total = array_reduce($portfolios, function ($carry, $item) {
            return bcadd($carry, $item->getAllocationPercent(), 4);
        }, '0');

        $this->assertEquals('0.9500', $total);

        foreach ($portfolios as $portfolio) {
            $percent = $portfolio->getAllocationPercent();
            $this->assertTrue($percent === '0.3166' || $percent === '0.3167');
        }
    }

    public function testPrePersistWithAllocatedPortfolio()
    {
        $entity = (new Portfolio)->setAllocationPercent('0.1000');
        $portfolios = [
            $this->createUnallocatedPortfolio(),
        ];
        $this->expectsAllocation('0.0500');
        $this->returnsPortfolios($portfolios);
        $args = new LifecycleEventArgs($entity, $this->manager);

        $this->subscriber->prePersist($args);

        $this->assertEquals('0.1000', $entity->getAllocationPercent());
        $this->assertEquals('0.8500', $portfolios[0]->getAllocationPercent());
    }

    public function testPrePersistWithUnallocatedPortfolioWithPercent()
    {
        $entity = (new Portfolio)->setUnallocated(true)->setAllocationPercent('0.5000');
        $portfolios = [
            $this->createUnallocatedPortfolio(),
        ];
        $this->expectsAllocation('0.0500');
        $this->returnsPortfolios($portfolios);
        $args = new LifecycleEventArgs($entity, $this->manager);

        $this->subscriber->prePersist($args);

        $this->assertEquals('0.4750', $portfolios[0]->getAllocationPercent());
        $this->assertEquals('0.4750', $entity->getAllocationPercent());
    }

    public function testPrePersistWithEmptyPortfolios()
    {
        $entity = (new Portfolio)->setUnallocated(true);
        $this->expectsAllocation('0.0500');
        $this->returnsPortfolios([]);
        $args = new LifecycleEventArgs($entity, $this->manager);

        $this->subscriber->prePersist($args);

        $this->assertEquals('0.9500', $entity->getAllocationPercent());
    }

    public function testSubscribedToPreUpdate()
    {
        $this->assertContains('preUpdate', $this->subscriber->getSubscribedEvents());
    }

    public function testPreUpdateOnlyOnPortfolioEntity()
    {
        $entity = new \stdClass;
        $changeset = [];
        $this->manager->expects($this->never())->method('getRepository');
        $args = new PreUpdateEventArgs($entity, $this->manager, $changeset);
        $this->subscriber->preUpdate($args);
    }

    public function testPreUpdateOnlyWhenAllocationChanged()
    {
        $entity = new Portfolio();
        $changeset = [];
        $this->manager->expects($this->never())->method('getRepository');
        $args = new PreUpdateEventArgs($entity, $this->manager, $changeset);
        $this->subscriber->preUpdate($args);
    }

    public function testPreUpdateAvoidInfiniteLoop()
    {
        $entity = (new Portfolio)->setUnallocated(true);
        $changeset = [
            'allocationPercent' => [null, null],
        ];
        $this->manager->expects($this->never())->method('getRepository');
        $args = new PreUpdateEventArgs($entity, $this->manager, $changeset);
        $this->subscriber->preUpdate($args);
    }

    public function testPreUpdateSetAllocationPercent()
    {
        $entity = (new Portfolio)->setAllocationPercent('0.1000');
        $portfolios = [
            $this->createUnallocatedPortfolio(),
        ];

        $changeset = [
            'allocationPercent' => [null, '0.1000'],
            'unallocated' => [true, false],
        ];

        $this->expectsAllocation('0.0500');
        $this->returnsPortfolios($portfolios);

        $args = new PreUpdateEventArgs($entity, $this->manager, $changeset);

        $this->subscriber->preUpdate($args);

        $this->assertEquals('0.1000', $entity->getAllocationPercent());
        $this->assertEquals('0.8500', $portfolios[0]->getAllocationPercent());
    }

    public function testPreUpdateSetUnallocated()
    {
        $entity = (new Portfolio)->setUnallocated(true);
        $portfolios = [
            $this->createUnallocatedPortfolio(),
        ];

        $changeset = [
            'allocationPercent' => ['0.1000', null],
            'unallocated' => [false, true],
        ];

        $this->expectsAllocation('0.1500');
        $this->returnsPortfolios($portfolios);
        $args = new PreUpdateEventArgs($entity, $this->manager, $changeset);

        $this->subscriber->preUpdate($args);

        $this->assertEquals('0.4750', $entity->getAllocationPercent());
        $this->assertEquals('0.4750', $portfolios[0]->getAllocationPercent());
    }

    public function testPreUpdateChangePercentButNotUnallocated()
    {
        $entity = (new Portfolio)->setName('Updated Entity')->setAllocationPercent('0.1500');
        $portfolios = [
            $this->createUnallocatedPortfolio(),
        ];

        $changeset = [
            'allocationPercent' => ['0.1000', '0.1500'],
        ];

        $this->expectsAllocation('0.1500');
        $this->returnsPortfoliosWithArgument($portfolios, $entity);
        $args = new PreUpdateEventArgs($entity, $this->manager, $changeset);

        $this->subscriber->preUpdate($args);

        $this->assertEquals('0.1500', $entity->getAllocationPercent());
        $this->assertEquals('0.8000', $portfolios[0]->getAllocationPercent());
    }

    public function testPreUpdateChangeUnallocatedButNotPercent()
    {
        $entity = (new Portfolio)->setAllocationPercent('0.1000');
        $portfolios = [
            $this->createUnallocatedPortfolio(),
        ];

        $changeset = [
            'unallocated' => [false, true],
        ];

        $this->expectsAllocation('0.1500');
        $this->returnsPortfoliosWithArgument($portfolios, $entity);
        $args = new PreUpdateEventArgs($entity, $this->manager, $changeset);

        $this->subscriber->preUpdate($args);

        $this->assertEquals('0.4750', $entity->getAllocationPercent());
        $this->assertEquals('0.4750', $portfolios[0]->getAllocationPercent());
    }

    public function testPreUpdateWithEmptyPortfolios()
    {
        $entity = (new Portfolio)->setUnallocated(true);

        $changeset = [
            'unallocated' => [false, true],
        ];

        $this->expectsAllocation('0.1500');
        $this->returnsPortfoliosWithArgument([], $entity);
        $args = new PreUpdateEventArgs($entity, $this->manager, $changeset);

        $this->subscriber->preUpdate($args);

        $this->assertEquals('0.8500', $entity->getAllocationPercent());
    }

    public function testSubscribedToPreRemove()
    {
        $this->assertContains('preRemove', $this->subscriber->getSubscribedEvents());
    }

    public function testPreRemoveOnlyOnPortfolioEntity()
    {
        $entity = new \stdClass;
        $this->manager->expects($this->never())->method('getRepository');
        $args = new LifecycleEventArgs($entity, $this->manager);
        $this->subscriber->preRemove($args);
    }

    public function testPreRemoveForAllocatedPortfolio()
    {
        $entity = (new Portfolio)->setAllocationPercent('0.1000');
        $portfolios = [
            $this->createUnallocatedPortfolio(),
        ];
        $this->expectsAllocation('0.1500');
        $this->returnsPortfolios($portfolios);
        $args = new LifecycleEventArgs($entity, $this->manager);

        $this->subscriber->preRemove($args);

        $this->assertEquals('0.9500', $portfolios[0]->getAllocationPercent());
    }

    public function testPreRemoveForUnallocatedPortfolio()
    {
        $entity = (new Portfolio)->setUnallocated(true);
        $portfolios = [
            $this->createUnallocatedPortfolio(),
        ];
        $this->expectsAllocation('0.0500');
        $this->returnsPortfoliosWithArgument($portfolios, $entity);
        $args = new LifecycleEventArgs($entity, $this->manager);

        $this->subscriber->preRemove($args);

        $this->assertEquals('0.9500', $portfolios[0]->getAllocationPercent());
    }

    public function testPreRemoveForUnallocatedPortfolioWithPercent()
    {
        $entity = (new Portfolio)->setUnallocated(true)->setAllocationPercent('0.4750');
        $portfolios = [
            $this->createUnallocatedPortfolio('0.4750'),
        ];
        $this->expectsAllocation('0.0500');
        $this->returnsPortfoliosWithArgument($portfolios, $entity);
        $args = new LifecycleEventArgs($entity, $this->manager);

        $this->subscriber->preRemove($args);

        $this->assertEquals('0.9500', $portfolios[0]->getAllocationPercent());
    }

    public function testPreRemoveWithEmptyPortfolios()
    {
        $entity = (new Portfolio)->setAllocationPercent('0.6000');
        $this->expectsAllocation('1.0000');
        $this->returnsPortfoliosWithArgument([], $entity);
        $args = new LifecycleEventArgs($entity, $this->manager);

        $this->subscriber->preRemove($args);
        // Nothing to test here since there are no unallocated portfolios
        // and the entity is being removed. Just ensure there are no errors.
    }

    private function createUnallocatedPortfolio(?string $percent = null): Portfolio
    {
        $portfolio = new Portfolio();
        $portfolio->setUnallocated(true);
        return $portfolio->setAllocationPercent($percent);
    }

    private function expectsAllocation($allocation, $matcher = null)
    {
        $this->repository->expects($matcher ?? $this->any())
            ->method('getPercentAllocated')
            ->willReturn($allocation)
        ;
    }

    private function returnsPortfolios(array $portfolios)
    {
        $this->repository->expects($this->once())
                ->method('findUnallocatedPortfolios')
                ->willReturn($portfolios)
            ;
    }

    private function returnsPortfoliosWithArgument(array $portfolios, Portfolio $entity)
    {
        $this->repository->expects($this->once())
                ->method('findUnallocatedPortfolios')
                ->with($entity)
                ->willReturn($portfolios)
            ;
    }
}
