<?php

namespace App\Service;

use App\Entity\Calculation;
use App\Enum\CalculatorOperationsEnum;
use App\Repository\CalculationRepository;
use Doctrine\DBAL\Exception;

class CalculationService
{
    public function __construct(
        private readonly CalculationRepository $calculatorRepository
    ) {
    }

    /**
     * @throws Exception
     */
    public function create(
        float $argumentA,
        float $argumentB,
        CalculatorOperationsEnum $operation,
    ): Calculation {
        return $this->calculatorRepository->add(
            new Calculation(
                argumentA: $argumentA,
                argumentB: $argumentB,
                operation: $operation,
            ),
        );
    }

    /**
     * @throws Exception
     */
    public function delete(Calculation $calculation): void
    {
        $this->calculatorRepository->remove($calculation);
    }

    /**
     * @throws Exception
     */
    public function createFromArray(array $calculationArray): Calculation
    {
        return $this->create(
            argumentA: $calculationArray['argumentA'],
            argumentB: $calculationArray['argumentB'],
            operation: $calculationArray['operation'],
        );
    }
}
