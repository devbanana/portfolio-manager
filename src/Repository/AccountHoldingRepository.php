<?php

namespace App\Repository;

use App\Entity\AccountHolding;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method AccountHolding|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccountHolding|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccountHolding[]    findAll()
 * @method AccountHolding[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountHoldingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Holding::class);
    }
}
