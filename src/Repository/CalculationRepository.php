<?php

namespace App\Repository;

use App\Entity\Calculation;
use App\Enum\CalculatorOperationsEnum;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

class CalculationRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Calculation::class);
    }

    public function findNoShownCalculations(): array {
        return $this->createQueryBuilder('c')
            ->where('c.isShown = false')
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findCalculationByArgumentsAndOperator(
        float $argumentA,
        float $argumentB,
        CalculatorOperationsEnum $operation,
    ): ?Calculation {
        return $this->createQueryBuilder('c')
            ->where('c.argumentA = :argumentA')
            ->andWhere('c.argumentB = :argumentB')
            ->andWhere('c.operation = :operation')
            ->setParameters([
                'argumentA' => $argumentA,
                'argumentB' => $argumentB,
                'operation' => $operation,
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }
}
