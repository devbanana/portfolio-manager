<?php

namespace App\Repository;

use App\Entity\PortfolioHolding;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PortfolioHolding|null find($id, $lockMode = null, $lockVersion = null)
 * @method PortfolioHolding|null findOneBy(array $criteria, array $orderBy = null)
 * @method PortfolioHolding[]    findAll()
 * @method PortfolioHolding[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PortfolioHoldingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PortfolioHolding::class);
    }

    public function getBalance(): string
    {
        return $this->createQueryBuilder('ph')
            ->select('sum(ph.totalValue)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
