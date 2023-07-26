<?php

namespace App\Entity;

use App\Enum\CalculatorOperationsEnum;
use App\Repository\CalculationRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CalculationRepository::class)]
class Calculation implements EntityInterface
{
    private const ENTITY_NAME = 'Вычисление';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(type: Types::FLOAT)]
    #[Assert\NotBlank(
        message: 'Значение не должно быть пустым',
    )]
    #[Assert\Type(
        type: 'float',
        message: 'Значение {{ value }} не соответствует типу {{ type }}.',
    )]
    private float $argumentA;

    #[ORM\Column(type: Types::FLOAT)]
    #[Assert\NotBlank(
        message: 'Значение не должно быть пустым',
    )]
    #[Assert\Type(
        type: 'float',
        message: 'Значение {{ value }} не соответствует типу {{ type }}.',
    )]
    private float $argumentB;

    #[ORM\Column(type: Types::STRING, enumType: CalculatorOperationsEnum::class)]
    #[Assert\NotBlank(
        message: 'Значение не должно быть пустым',
    )]
    #[Assert\Choice(
        choices: CalculatorOperationsEnum::VALID_VALUES,
        message: 'Выберите операцию из списка'
    )]
    private CalculatorOperationsEnum $operation;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    public function __construct(
        float $argumentA,
        float $argumentB,
        CalculatorOperationsEnum $operation,
    ) {
        $this->argumentA = $argumentA;
        $this->argumentB = $argumentB;
        $this->operation = $operation;
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getArgumentA(): float
    {
        return $this->argumentA;
    }

    public function getArgumentB(): float
    {
        return $this->argumentB;
    }

    public function getOperation(): CalculatorOperationsEnum
    {
        return $this->operation;
    }

    public function setArgumentA(float $argumentA): Calculation
    {
        $this->argumentA = $argumentA;

        return $this;
    }

    public function setArgumentB(float $argumentB): Calculation
    {
        $this->argumentB = $argumentB;

        return $this;
    }

    public function setOperation(CalculatorOperationsEnum $operation): Calculation
    {
        $this->operation = $operation;

        return $this;
    }

    public static function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): Calculation
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function calculate(): float
    {
        return match ($this->getOperation()) {
            CalculatorOperationsEnum::ADDITION => $this->getArgumentA() + $this->getArgumentB(),
            CalculatorOperationsEnum::SUBTRACTION => $this->getArgumentA() - $this->getArgumentB(),
            CalculatorOperationsEnum::MULTIPLICATION => $this->getArgumentA() * $this->getArgumentB(),
            CalculatorOperationsEnum::DIVISION => $this->getArgumentA() / $this->getArgumentB(),
        };
    }
}
