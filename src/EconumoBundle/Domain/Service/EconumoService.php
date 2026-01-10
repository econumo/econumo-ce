<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service;


readonly class EconumoService implements EconumoServiceInterface
{
    public function __construct(
        private string $baseUrl,
        private bool $connectUsers
    ) {
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function connectUsers(): bool
    {
        return $this->connectUsers;
    }
}
