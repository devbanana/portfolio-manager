<?php

declare(strict_types = 1);

namespace App\EventListener;

use App\Entity\Portfolio;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Common\Persistence\ObjectManager;

class UpdateAllocationSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
            Events::preRemove,
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if (!($entity instanceof Portfolio)) {
            return;
        }

        $manager = $args->getObjectManager();
        $repository = $manager->getRepository(Portfolio::class);

        $percent = $repository->getPercentAllocated();
        $portfolios = $repository->findUnallocatedPortfolios();

        // Add to all portfolios who are unallocated
        if ($entity->isUnallocated()) {
            $portfolios[] = $entity;
        } else {
            $percent = bcadd($percent, $entity->getAllocationPercent() ?? '0', 4);
        }

        $this->calculateUnallocatedPercents($portfolios, $percent);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getObject();
        if (!($entity instanceof Portfolio)) {
            return;
        }

        // We only care if allocation has been changed
        if (!$args->hasChangedField('allocationPercent') && !$args->hasChangedField('unallocated')) {
            return;
        }

        // Prevent updating if unallocated is null and only percent has been changed
        // Otherwise we'd have an infinite loop of updates
        if ($entity->isUnallocated() === true && !$args->hasChangedField('unallocated')) {
            return;
        }

        $manager = $args->getObjectManager();
        $repository = $manager->getRepository(Portfolio::class);

        $percent = $repository->getPercentAllocated();
        $portfolios = $repository->findUnallocatedPortfolios($entity);

        if ($args->hasChangedField('unallocated')) {
            if ($args->getNewValue('unallocated') === true) {
                // Subtract old percent
                $oldPercent = $args->hasChangedField('allocationPercent')
                    ? $args->getOldValue('allocationPercent')
                    : $entity->getAllocationPercent();
                $percent = bcsub($percent, $oldPercent ?? '0', 4);

                // Add to portfolios array
                $portfolios[] = $entity;
            } else {
                // Add new percent
                $newPercent = $args->hasChangedField('allocationPercent')
                    ? $args->getNewValue('allocationPercent')
                    : $entity->getAllocationPercent();
                $percent = bcadd($percent, $newPercent, 4);
            }
        } else {
            $percent = bcsub($percent, $args->getOldValue('allocationPercent'), 4);
            $percent = bcadd($percent, $args->getNewValue('allocationPercent'), 4);
        }

        $this->calculateUnallocatedPercents($portfolios, $percent);
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if (!($entity instanceof Portfolio)) {
            return;
        }

        $manager = $args->getObjectManager();
        $repository = $manager->getRepository(Portfolio::class);

        $percent = $repository->getPercentAllocated();
        $portfolios = $repository->findUnallocatedPortfolios($entity);

        if (!$entity->isUnallocated()) {
            $percent = bcsub($percent, $entity->getAllocationPercent() ?? '0', 4);
        }

        $this->calculateUnallocatedPercents($portfolios, $percent);
    }

    private function calculateUnallocatedPercents(array $portfolios, string $allocatedPercent): void
    {
        // Return if there are no portfolios
        if (empty($portfolios)) {
            return;
        }

        $unallocated = bcsub('1', $allocatedPercent, 4);
        $perPortfolio = bcdiv($unallocated, (string)count($portfolios), 4);

        foreach ($portfolios as $portfolio) {
            $portfolio->setAllocationPercent($perPortfolio);
        }

        $allocated = bcmul($perPortfolio, (string)count($portfolios), 4);
        if (bccomp($unallocated, $allocated, 4) === 1) {
            // Add to portfolios until they balance
            $diff = bcsub($unallocated, $allocated, 4);
            $remainder = bcdiv($diff, '0.0001', 0);
            shuffle($portfolios);
            for ($i = 0; $i < $remainder; $i++) {
                $portfolios[$i]->setAllocationPercent(bcadd($portfolios[$i]->getAllocationPercent(), '0.0001', 4));
            }
        }
    }
}
