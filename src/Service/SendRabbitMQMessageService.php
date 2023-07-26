<?php

namespace App\Service;

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;

class SendRabbitMQMessageService implements SendMessageInterface
{
    public function __construct(
        private readonly ProducerInterface $producer
    ) {
    }

    public function getProducer(): ProducerInterface
    {
        return $this->producer;
    }

    public function sendMessage(array $message): void
    {
        $this->getProducer()->publish(serialize($message));
    }
}
