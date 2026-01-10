<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service\Currency;

use DateTimeImmutable;
use App\EconumoBundle\Domain\Entity\ValueObject\CurrencyCode;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use App\EconumoBundle\Domain\Exception\NotFoundException;
use App\EconumoBundle\Domain\Repository\CurrencyRateRepositoryInterface;
use App\EconumoBundle\Domain\Repository\CurrencyRepositoryInterface;
use App\EconumoBundle\Domain\Service\Currency\Dto\AverageCurrencyRatesDto;
use App\EconumoBundle\Domain\Service\Dto\FullCurrencyRateDto;
use DateTimeInterface;
use Doctrine\ORM\NoResultException;

readonly class CurrencyRateService implements CurrencyRateServiceInterface
{
    private CurrencyCode $baseCurrency;

    public function __construct(
        string $baseCurrency,
        private CurrencyRateRepositoryInterface $currencyRateRepository,
        private CurrencyRepositoryInterface $currencyRepository
    ) {
        $this->baseCurrency = new CurrencyCode($baseCurrency);
    }

    /**
     * @inheritDoc
     */
    public function getCurrencyRates(DateTimeInterface $dateTime): array
    {
        return $this->currencyRateRepository->getAll($dateTime);
    }

    /**
     * @inheritDoc
     */
    public function getLatestCurrencyRates(): array
    {
        try {
            $result = $this->currencyRateRepository->getAll();
        } catch (NoResultException) {
            $result = [];
        }

        return $result;
    }

    public function getChanged(DateTimeInterface $lastUpdate): array
    {
        $currencyRates = $this->getLatestCurrencyRates();
        $result = [];
        foreach ($currencyRates as $currencyRate) {
            if ($currencyRate->getPublishedAt() > $lastUpdate) {
                $result[] = $currencyRate;
            }
        }

        return $result;
    }

    public function getAverageCurrencyRates(
        DateTimeInterface $startDate,
        DateTimeInterface $endDate
    ): AverageCurrencyRatesDto {
        $baseCurrency = $this->currencyRepository->getByCode($this->baseCurrency);
        try {
            $lastDate = $this->currencyRateRepository->getLatestDate($baseCurrency->getId(), $endDate);
            $realPeriodStart = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $lastDate->format('Y-m-01 00:00:00'));
            $realPeriodEnd = $realPeriodStart->modify('next month');
        } catch (NotFoundException) {
            $lastDate = null;
            $realPeriodStart = $startDate;
            $realPeriodEnd = $endDate;
        }

        return new AverageCurrencyRatesDto(
            $baseCurrency,
            $realPeriodStart,
            $realPeriodEnd,
            $this->currencyRateRepository->getAverage($realPeriodStart, $realPeriodEnd, $baseCurrency->getId())
        );
    }

    public function getAverageFullCurrencyRatesDtos(
        DateTimeInterface $startDate,
        DateTimeInterface $endDate
    ): array {
        $averageCurrencyRatesDto = $this->getAverageCurrencyRates($startDate, $endDate);
        $data = [];
        foreach ($averageCurrencyRatesDto->rates as $rate) {
            $data[$rate['currencyId']] = [
                'id' => new Id($rate['currencyId']),
                'rate' => $rate['rate'],
            ];
        }

        $currencies = $this->currencyRepository->getByIds(array_column($data, 'id'));
        $result = [];
        foreach ($currencies as $currency) {
            if (!isset($data[$currency->getId()->getValue()])) {
                continue;
            }

            $dto = new FullCurrencyRateDto();
            $dto->baseCurrencyId = $averageCurrencyRatesDto->baseCurrency->getId();
            $dto->baseCurrencyCode = $averageCurrencyRatesDto->baseCurrency->getCode();
            $dto->currencyId = $currency->getId();
            $dto->currencyCode = $currency->getCode();
            $dto->rate = new DecimalNumber($data[$currency->getId()->getValue()]['rate']);
            $dto->date = $averageCurrencyRatesDto->periodStart;
            $result[] = $dto;
        }

        return $result;
    }
}
