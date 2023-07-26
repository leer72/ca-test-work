<?php

namespace App\Service;

use Doctrine\DBAL\Exception;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class CalculatorConsumer implements ConsumerInterface
{
    public function __construct(
        private readonly CalculationService $calculationService
    ) {
    }

    /**
     * @throws Exception
     * @return void
     * @var AMQPMessage $msg
     */
    public function execute(AMQPMessage $msg): void
    {
        $calculationArray = unserialize($msg->getBody());

        $calculation = $this->calculationService->createFromArray($calculationArray);

        echo 'Создано вычисление с ID: ' . $calculation->getId() . PHP_EOL;
    }
}
