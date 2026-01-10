<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service;


interface EconumoServiceInterface
{
    public function getBaseUrl(): string;

    public function connectUsers(): bool;
}
