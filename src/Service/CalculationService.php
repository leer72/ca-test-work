<?php

namespace App\Service;

use App\Entity\Calculation;
use App\Enum\CalculatorOperationsEnum;
use App\Repository\CalculationRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityNotFoundException;

class CalculationService
{
    public const CALCULATION_ID_ARRAY_KEY = 'calculation_id';

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
     * @throws EntityNotFoundException
     */
    public function calculate(array $calculationArray): Calculation
    {
        if (!isset($calculationArray[self::CALCULATION_ID_ARRAY_KEY])) {
            throw new Exception('Не передан идентификатор вычисления');
        }

        /** @var Calculation $calculation */
        $calculation = $this->calculatorRepository->findOneBy(
            ['id' => $calculationArray[self::CALCULATION_ID_ARRAY_KEY]]
        );

        if (is_null($calculation)) {
            throw new EntityNotFoundException('Вычисление не найдено');
        }

        $calculation->calculate();
        $this->calculatorRepository->flush();

        return $calculation;
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
