<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service;


interface AntiCorruptionServiceInterface
{
    public function beginTransaction(string $name): void;

    public function commit(string $name): void;

    public function rollback(string $name): void;
}
