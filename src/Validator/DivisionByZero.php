<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class DivisionByZero extends Constraint
{
    public string $message = 'На ноль делить нельзя';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
