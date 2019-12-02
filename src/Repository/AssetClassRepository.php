<?php

namespace App\Repository;

use App\Entity\AssetClass;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method AssetClass|null find($id, $lockMode = null, $lockVersion = null)
 * @method AssetClass|null findOneBy(array $criteria, array $orderBy = null)
 * @method AssetClass[]    findAll()
 * @method AssetClass[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssetClassRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AssetClass::class);
    }

    /**
     * Get all asset classes excluding cash.
     *
     * We don't want to return cash because the user can't generally act on it.
     *
     * New assets cannot be created with the cash asset class,
     * so we want to exclude it as an option.
     *
     * @return AssetClass[] Returns an array of AssetClass objects
     */
    public function findAllExcludingCash(): array
    {
        return $this->createQueryBuilder('ac')
            ->andWhere('ac.name != :class')
            ->setParameter('class', 'Cash')
            ->orderBy('ac.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
