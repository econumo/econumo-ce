<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service\Currency;


use App\EconumoBundle\Domain\Entity\Currency;
use App\EconumoBundle\Domain\Entity\CurrencyRate;
use App\EconumoBundle\Domain\Entity\ValueObject\CurrencyCode;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use App\EconumoBundle\Domain\Exception\DomainException;
use App\EconumoBundle\Domain\Repository\CurrencyRateRepositoryInterface;
use App\EconumoBundle\Domain\Repository\CurrencyRepositoryInterface;
use App\EconumoBundle\Domain\Service\Currency\Dto\CurrencyConvertorDto;
use App\EconumoBundle\Domain\Service\Dto\FullCurrencyRateDto;
use DateTimeInterface;

class CurrencyConvertor implements CurrencyConvertorInterface
{
    private readonly CurrencyCode $baseCurrency;

    private ?Id $baseCurrencyId = null;

    /**
     * @var Currency[]
     */
    private array $currenciesCacheByCode = [];

    /**
     * @var Currency[]
     */
    private array $currenciesCacheById = [];

    public function __construct(
        string $baseCurrency,
        readonly private CurrencyRateRepositoryInterface $currencyRateRepository,
        readonly private CurrencyRateServiceInterface $currencyRateService,
        readonly private CurrencyRepositoryInterface $currencyRepository
    ) {
        $this->baseCurrency = new CurrencyCode($baseCurrency);
    }

    public function convert(CurrencyCode $originalCurrency, CurrencyCode $resultCurrency, DecimalNumber $sum): DecimalNumber
    {
        if ($originalCurrency->isEqual($resultCurrency)) {
            return $sum;
        }

        $rates = [];
        foreach ($this->currencyRateRepository->getAll() as $currencyRate) {
            $rates[] = $this->transformCurrencyToDto($currencyRate);
        }

        return $this->convertInternal($rates, $originalCurrency, $resultCurrency, $sum);
    }

    /**
     * @inheritDoc
     */
    public function bulkConvert(DateTimeInterface $periodStart, DateTimeInterface $periodEnd, array $items): array
    {
        $conversionNeeded = [];
        $currentPeriodStartIndex = $periodStart->format('Ym');
        $conversionNeeded[$currentPeriodStartIndex] = [$periodStart, $periodEnd];
        foreach ($items as $item) {
            if (is_array($item)) {
                foreach ($item as $subItem) {
                    $index = $subItem->periodStart->format('Ym');
                    if ($subItem->fromCurrencyId->isEqual($subItem->toCurrencyId)) {
                        continue;
                    }

                    if (array_key_exists($index, $conversionNeeded)) {
                        continue;
                    }

                    $conversionNeeded[$index] = [$subItem->periodStart, $subItem->periodEnd];
                }
            } else {
                $index = $item->periodStart->format('Ym');
                if ($item->fromCurrencyId->isEqual($item->toCurrencyId)) {
                    continue;
                }

                if (array_key_exists($index, $conversionNeeded)) {
                    continue;
                }

                $conversionNeeded[$index] = [$item->periodStart, $item->periodEnd];
            }
        }

        if ($conversionNeeded === []) {
            return [];
        }

        $rates = [];
        foreach ($conversionNeeded as $index => $dateRange) {
            $rates[$index] = $this->currencyRateService->getAverageFullCurrencyRatesDtos($dateRange[0], $dateRange[1]);
        }

        $result = [];
        $flatItems = [];
        foreach ($items as $key => $dto) {
            if ($dto instanceof CurrencyConvertorDto) {
                $flatItems[$key][] = $dto;
            } else {
                foreach ($this->summarizeDtosByCurrency($dto) as $item) {
                    $flatItems[$key][] = $item;
                }
            }
        }

        foreach ($flatItems as $key => $dtos) {
            $result[$key] = new DecimalNumber();
            foreach ($this->summarizeDtosByCurrency($dtos) as $item) {
                $existingKey = array_key_exists($item->periodStart->format('Ym'), $rates) ? $item->periodStart->format('Ym') : $currentPeriodStartIndex;
                $result[$key] = $result[$key]->add($this->convertInternalById($rates[$existingKey], $item->fromCurrencyId, $item->toCurrencyId, $item->amount));
            }
        }

        return $result;
    }

    /**
     * @param FullCurrencyRateDto[] $rates
     */
    private function convertInternal(
        array $rates,
        CurrencyCode $originalCurrency,
        CurrencyCode $resultCurrency,
        DecimalNumber $sum
    ): DecimalNumber {
        if ($originalCurrency->isEqual($resultCurrency)) {
            return $sum;
        }

        $result = new DecimalNumber($sum->getValue());
        if (!$originalCurrency->isEqual($this->baseCurrency)) {
            foreach ($rates as $rate) {
                if ($rate->currencyCode->isEqual($originalCurrency)) {
                    $result = $result->div($rate->rate);
                    break;
                }
            }
        }

        if (!$resultCurrency->isEqual($this->baseCurrency)) {
            foreach ($rates as $rate) {
                if ($rate->currencyCode->isEqual($resultCurrency)) {
                    $result = $result->mul($rate->rate);
                    break;
                }
            }
        }

        if (!array_key_exists($resultCurrency->getValue(), $this->currenciesCacheByCode)) {
            $tmpResultCurrency = $this->currencyRepository->getByCode($resultCurrency);
            $this->currenciesCacheByCode[$tmpResultCurrency->getCode()->getValue()] = $tmpResultCurrency;
            $this->currenciesCacheById[$tmpResultCurrency->getId()->getValue()] = $tmpResultCurrency;
        }

        return $result->round($this->currenciesCacheByCode[$resultCurrency->getValue()]->getFractionDigits());
    }

    /**
     * @param FullCurrencyRateDto[] $rates
     */
    private function convertInternalById(
        array $rates,
        Id $originalCurrencyId,
        Id $resultCurrencyId,
        DecimalNumber $amount
    ): DecimalNumber {
        if ($originalCurrencyId->isEqual($resultCurrencyId)) {
            return $amount;
        }

        if ($this->baseCurrencyId instanceof Id) {
            $baseCurrencyId = $this->baseCurrencyId;
        } else {
            $baseCurrency = $this->currencyRepository->getByCode($this->baseCurrency);
            if (!$baseCurrency instanceof Currency) {
                throw new DomainException('Base Currency not found');
            }

            $baseCurrencyId = $baseCurrency->getId();
            $this->baseCurrencyId = $baseCurrencyId;
        }

        $result = $amount;
        if (!$originalCurrencyId->isEqual($baseCurrencyId)) {
            foreach ($rates as $rate) {
                if ($rate->currencyId->isEqual($originalCurrencyId)) {
                    $result = $result->div($rate->rate);
                    break;
                }
            }
        }

        if (!$resultCurrencyId->isEqual($baseCurrencyId)) {
            foreach ($rates as $rate) {
                if ($rate->currencyId->isEqual($resultCurrencyId)) {
                    $result = $result->mul($rate->rate);
                    break;
                }
            }
        }

        if (!array_key_exists($resultCurrencyId->getValue(), $this->currenciesCacheById)) {
            $tmpResultCurrency = $this->currencyRepository->get($resultCurrencyId);
            $this->currenciesCacheByCode[$tmpResultCurrency->getCode()->getValue()] = $tmpResultCurrency;
            $this->currenciesCacheById[$tmpResultCurrency->getId()->getValue()] = $tmpResultCurrency;
        }

        return $result->round($this->currenciesCacheById[$resultCurrencyId->getValue()]->getFractionDigits());
    }

    private function transformCurrencyToDto(CurrencyRate $currency): FullCurrencyRateDto
    {
        $dto = new FullCurrencyRateDto();
        $dto->currencyId = $currency->getCurrency()->getId();
        $dto->currencyCode = $currency->getCurrency()->getCode();
        $dto->baseCurrencyId = $currency->getBaseCurrency()->getId();
        $dto->baseCurrencyCode = $currency->getBaseCurrency()->getCode();
        $dto->rate = $currency->getRate();
        $dto->date = $currency->getPublishedAt();

        return $dto;
    }



    /**
     * @param CurrencyConvertorDto[] $dtos
     * @return CurrencyConvertorDto[]
     */
    private function summarizeDtosByCurrency(array $dtos): array
    {
        /** @var CurrencyConvertorDto[] $result */
        $result = [];
        foreach ($dtos as $dto) {
            $index = sprintf('%s-%s_%s-%s',
                $dto->fromCurrencyId->getValue(),
                $dto->toCurrencyId->getValue(),
                $dto->periodStart->format('Y-m-d'),
                $dto->periodEnd->format('Y-m-d')
            );
            if (!array_key_exists($index, $result)) {
                $result[$index] = new CurrencyConvertorDto(
                    $dto->periodStart,
                    $dto->periodEnd,
                    $dto->fromCurrencyId,
                    $dto->toCurrencyId,
                    $dto->amount
                );
            } else {
                $result[$index] = new CurrencyConvertorDto(
                    $dto->periodStart,
                    $dto->periodEnd,
                    $dto->fromCurrencyId,
                    $dto->toCurrencyId,
                    $result[$index]->amount->add($dto->amount),
                );
            }
        }

        return array_values($result);
    }
}
