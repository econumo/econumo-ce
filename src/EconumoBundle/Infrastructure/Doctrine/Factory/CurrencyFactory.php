<?php

declare(strict_types=1);

namespace App\EconumoBundle\Infrastructure\Doctrine\Factory;

use App\EconumoBundle\Domain\Entity\Currency;
use App\EconumoBundle\Domain\Entity\ValueObject\CurrencyCode;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use App\EconumoBundle\Domain\Factory\CurrencyFactoryInterface;
use App\EconumoBundle\Domain\Repository\CurrencyRepositoryInterface;
use App\EconumoBundle\Domain\Service\DatetimeServiceInterface;
use App\EconumoBundle\Domain\Service\Dto\CurrencyDto;
use Symfony\Component\Intl\Currencies;
use Symfony\Component\Intl\Exception\MissingResourceException;

readonly class CurrencyFactory implements CurrencyFactoryInterface
{

    public function __construct(
        private CurrencyRepositoryInterface $currencyRepository,
        private DatetimeServiceInterface $datetimeService
    ) {
    }

    public function createByCode(CurrencyCode $code): Currency
    {
        try {
            $symbol = Currencies::getSymbol($code->getValue());
            $fractionDigits = Currencies::getFractionDigits($code->getValue());
        } catch (MissingResourceException) {
            $symbol = $code->getValue();
            $fractionDigits = DecimalNumber::SCALE;
        }

        return new Currency(
            $this->currencyRepository->getNextIdentity(),
            $code,
            $symbol,
            null,
            $fractionDigits,
            $this->datetimeService->getCurrentDatetime()
        );
    }

    public function create(CurrencyDto $dto): Currency
    {
        try {
            $symbol = Currencies::getSymbol($dto->code->getValue());
            $fractionDigits = Currencies::getFractionDigits($dto->code->getValue());
        } catch (MissingResourceException) {
            $symbol = $dto->code->getValue();
            $fractionDigits = DecimalNumber::SCALE;
        }

        if (empty($dto->symbol)) {
            $dto->symbol = $symbol;
        }

        if ($dto->fractionDigits === null) {
            $dto->fractionDigits = $fractionDigits;
        }

        return new Currency(
            $this->currencyRepository->getNextIdentity(),
            $dto->code,
            $dto->symbol,
            $dto->name,
            $dto->fractionDigits,
            $this->datetimeService->getCurrentDatetime()
        );
    }
}
