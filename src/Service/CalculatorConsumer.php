<?php

namespace App\Service;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityNotFoundException;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class CalculatorConsumer implements ConsumerInterface
{
    public function __construct(
        private readonly CalculationService $calculationService
    ) {
    }

    /**
     * @param AMQPMessage $msg
     * @return void
     * @throws EntityNotFoundException
     * @throws Exception
     */
    public function execute(AMQPMessage $msg): void
    {
        $calculationArray = unserialize($msg->getBody());

        $calculation = $this->calculationService->calculate($calculationArray);

        echo 'Создано вычисление с ID: ' . $calculation->getId() . PHP_EOL;
    }
}
