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

    // /**
    //  * @return PortfolioHolding[] Returns an array of PortfolioHolding objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PortfolioHolding
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
