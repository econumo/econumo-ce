<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service\Currency;

use App\EconumoBundle\Domain\Entity\Currency;
use DateTimeInterface;

interface CurrencyServiceInterface
{
    public function getBaseCurrency(): Currency;

    /**
     * @return Currency[]
     */
    public function getAvailableCurrencies(): array;
}
