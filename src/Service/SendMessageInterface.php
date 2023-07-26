<?php

namespace App\Service;

interface SendMessageInterface
{
    public function sendMessage(array $message): void;
}
