<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service\Currency;

use App\EconumoBundle\Domain\Entity\Currency;
use App\EconumoBundle\Domain\Entity\CurrencyRate;
use App\EconumoBundle\Domain\Entity\ValueObject\CurrencyCode;
use App\EconumoBundle\Domain\Exception\NotFoundException;
use App\EconumoBundle\Domain\Factory\CurrencyRateFactoryInterface;
use App\EconumoBundle\Domain\Repository\CurrencyRateRepositoryInterface;
use App\EconumoBundle\Domain\Repository\CurrencyRepositoryInterface;

readonly class CurrencyRatesUpdateService implements CurrencyRatesUpdateServiceInterface
{
    public function __construct(
        private CurrencyRateRepositoryInterface $currencyRateRepository,
        private CurrencyRepositoryInterface $currencyRepository,
        private CurrencyRateFactoryInterface $currencyRateFactory
    ) {
    }

    /**
     * @inheritDoc
     */
    public function updateCurrencyRates(array $currencyRates): int
    {
        $currencies = $this->currencyRepository->getAll();

        /** @var CurrencyRate[] $forUpdate */
        $forUpdate = [];
        foreach ($currencyRates as $currencyRateDto) {
            $currency = $this->getCurrency($currencies, $currencyRateDto->code);
            $baseCurrency = $this->getCurrency($currencies, $currencyRateDto->base);
            try {
                $item = $this->currencyRateRepository->get($currency->getId(), $baseCurrency->getId(), $currencyRateDto->date);
                $item->updateRate($currencyRateDto->rate);
            } catch (NotFoundException) {
                $item = $this->currencyRateFactory->create(
                    $currencyRateDto->date,
                    $currency,
                    $baseCurrency,
                    $currencyRateDto->rate
                );
            }

            $forUpdate[] = $item;
        }

        if ($forUpdate !== []) {
            $this->currencyRateRepository->save($forUpdate);
        }

        return count($forUpdate);
    }

    /**
     * @param Currency[] $currencies
     */
    private function getCurrency(array $currencies, CurrencyCode $code): Currency
    {
        foreach ($currencies as $currency) {
            if ($currency->getCode()->isEqual($code)) {
                return $currency;
            }
        }

        throw new NotFoundException('Not found currency ' . $code->getValue());
    }
}
