<?php

namespace App\Enum;

enum CalculatorOperationsEnum: string
{
    case ADDITION = '+';
    case SUBTRACTION = '-';
    case MULTIPLICATION = '*';
    case DIVISION = '/';

    public const VALID_VALUES = [
        self::ADDITION,
        self::SUBTRACTION,
        self::MULTIPLICATION,
        self::DIVISION,
    ];
}
