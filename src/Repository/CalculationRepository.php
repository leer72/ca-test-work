<?php

namespace App\Repository;

use App\Entity\Calculation;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

class CalculationRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Calculation::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findFirstCalculation(): ?Calculation
    {
        return $this->createQueryBuilder('Calculation')
            ->orderBy('Calculation.createdAt', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
