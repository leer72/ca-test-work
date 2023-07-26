<?php

namespace App\Entity;

interface EntityInterface
{
    public function getId(): int|string;

    public static function getEntityName(): string;
}
