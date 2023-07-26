<?php

namespace App\Service;

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;

class CalculatorProducer
{
    public function __construct(
        private readonly ProducerInterface $producer
    ) {
    }

    public function getProducer(): ProducerInterface
    {
        return $this->producer;
    }
}
