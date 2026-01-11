<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service\Currency;

use App\EconumoBundle\Domain\Entity\ValueObject\CurrencyCode;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use App\EconumoBundle\Domain\Factory\CurrencyFactoryInterface;
use App\EconumoBundle\Domain\Repository\CurrencyRepositoryInterface;
use Symfony\Component\Intl\Currencies;
use Symfony\Component\Intl\Exception\MissingResourceException;

readonly class CurrencyUpdateService implements CurrencyUpdateServiceInterface
{
    public function __construct(
        private CurrencyRepositoryInterface $currencyRepository,
        private CurrencyFactoryInterface $currencyFactory
    ) {
    }

    /**
     * @inheritDoc
     */
    public function updateCurrencies(array $currencies): void
    {
        $savedCurrencies = $this->currencyRepository->getAll();
        $updatedCurrencies = [];
        foreach ($currencies as $currencyDto) {
            $found = false;
            foreach ($savedCurrencies as $savedCurrency) {
                if ($savedCurrency->getCode()->isEqual($currencyDto->code)) {
                    $found = true;
                    $modified = false;
                    if ($currencyDto->name !== null) {
                        $savedCurrency->updateName($currencyDto->name);
                        $modified = true;
                    }

                    if ($currencyDto->symbol !== null) {
                        $savedCurrency->updateSymbol($currencyDto->symbol);
                        $modified = true;
                    }

                    if ($currencyDto->fractionDigits !== null) {
                        $savedCurrency->updateFractionDigits($currencyDto->fractionDigits);
                        $modified = true;
                    }

                    if ($modified) {
                        $updatedCurrencies[] = $savedCurrency;
                    }

                    break;
                }
            }

            if (!$found) {
                $updatedCurrencies[] = $this->currencyFactory->create($currencyDto);
            }
        }

        if ($updatedCurrencies === []) {
            return;
        }

        $this->currencyRepository->save($updatedCurrencies);
    }

    public function restoreFractionDigits(array $currencies): void
    {
        $savedCurrencies = $this->currencyRepository->getAll();
        $updatedCurrencies = [];
        foreach ($currencies as $currencyDto) {
            foreach ($savedCurrencies as $savedCurrency) {
                if ($savedCurrency->getCode()->isEqual($currencyDto->code)) {
                    $systemFractionDigits = $this->getSystemFractionDigits($currencyDto->code);
                    if ($systemFractionDigits !== $savedCurrency->getFractionDigits()) {
                        $savedCurrency->updateFractionDigits($this->getSystemFractionDigits($currencyDto->code));
                        $updatedCurrencies[] = $savedCurrency;
                    }

                    break;
                }
            }
        }

        if ($updatedCurrencies === []) {
            return;
        }

        $this->currencyRepository->save($updatedCurrencies);
    }

    private function getSystemFractionDigits(CurrencyCode $code): int
    {
        try {
            Currencies::getName($code->getValue());
            $fractionDigits = Currencies::getFractionDigits($code->getValue());
        } catch (MissingResourceException) {
            $fractionDigits = DecimalNumber::SCALE;
        }

        return $fractionDigits;
    }
}
