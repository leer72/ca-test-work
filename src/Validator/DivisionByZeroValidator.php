<?php

namespace App\Validator;

use App\Enum\CalculatorOperationsEnum;
use App\Form\Model\CalculationFormModel;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class DivisionByZeroValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$value instanceof CalculationFormModel) {
            throw new UnexpectedValueException($value, Constraint::class);
        }

        if (!$constraint instanceof DivisionByZero) {
            throw new UnexpectedValueException($constraint, DivisionByZero::class);
        }

        $isDivisionByZero = $value->operation === CalculatorOperationsEnum::DIVISION &&
            $value->argumentB == 0;

        if ($isDivisionByZero) {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath('calculation.argumentB')
                ->addViolation();
        }
    }
}
