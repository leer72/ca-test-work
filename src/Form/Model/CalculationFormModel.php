<?php

namespace App\Form\Model;

use App\Enum\CalculatorOperationsEnum;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as MyValidator;

#[MyValidator\DivisionByZero]
class CalculationFormModel
{
    #[Assert\NotBlank(
        message: 'Значение не должно быть пустым',
    )]
    #[Assert\Type(
        type: 'float',
        message: 'Значение {{ value }} не соответствует типу {{ type }}.',
    )]
    public $argumentA;

    #[Assert\NotBlank(
        message: 'Значение не должно быть пустым',
    )]
    #[Assert\Type(
        type: 'float',
        message: 'Значение {{ value }} не соответствует типу {{ type }}.',
    )]
    public $argumentB;

    #[Assert\NotBlank(
        message: 'Значение не должно быть пустым',
    )]
    #[Assert\Choice(
        choices: CalculatorOperationsEnum::VALID_VALUES,
        message: 'Выберите операцию из списка'
    )]
    public $operation;
}
