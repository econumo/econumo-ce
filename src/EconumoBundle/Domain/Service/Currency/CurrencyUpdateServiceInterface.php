<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service\Currency;


use App\EconumoBundle\Domain\Service\Dto\CurrencyDto;

interface CurrencyUpdateServiceInterface
{
    /**
     * @param CurrencyDto[] $currencies
     * @return void
     */
    public function updateCurrencies(array $currencies): void;

    /**
     * @param CurrencyDto[] $currencies
     * @return void
     */
    public function restoreFractionDigits(array $currencies): void;
}
