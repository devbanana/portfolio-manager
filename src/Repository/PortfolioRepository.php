<?php

namespace App\Repository;

use App\Entity\Portfolio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Portfolio|null find($id, $lockMode = null, $lockVersion = null)
 * @method Portfolio|null findOneBy(array $criteria, array $orderBy = null)
 * @method Portfolio[]    findAll()
 * @method Portfolio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PortfolioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Portfolio::class);
    }

    public function findUnallocatedPortfolios(?Portfolio $exclude = null): array
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.unallocated = true')
        ;

        if ($exclude) {
            $qb->andWhere('p.id != ' . $exclude->getId());
        }

        return $qb->getQuery()->getResult();
    }

    public function getPercentAllocated(): string
    {
        return $this->createQueryBuilder('p')
            ->select('sum(p.allocationPercent)')
            ->where('p.unallocated = false')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
