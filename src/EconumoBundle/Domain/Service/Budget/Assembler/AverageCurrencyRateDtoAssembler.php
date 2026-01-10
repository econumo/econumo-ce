<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service\Budget\Assembler;


use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use App\EconumoBundle\Domain\Service\Budget\Dto\AverageCurrencyRateDto;
use App\EconumoBundle\Domain\Service\Currency\CurrencyRateServiceInterface;
use DateTimeInterface;

readonly class AverageCurrencyRateDtoAssembler
{
    public function __construct(
        private CurrencyRateServiceInterface $currencyRateService
    ) {
    }

    /**
     * @param Id[] $currenciesIds
     * @return AverageCurrencyRateDto[]
     */
    public function assemble(
        DateTimeInterface $periodStart,
        DateTimeInterface $periodEnd,
        array $currenciesIds = []
    ): array {
        $averageCurrencyRatesDto = $this->currencyRateService->getAverageCurrencyRates($periodStart, $periodEnd);
        $supportedCurrencyIds = [];
        if ($currenciesIds !== []) {
            $supportedCurrencyIds = array_map(static fn(Id $id): string => $id->getValue(), $currenciesIds);
        }

        $result = [];
        foreach ($averageCurrencyRatesDto->rates as $item) {
            if ($supportedCurrencyIds === [] || in_array($item['currencyId'], $supportedCurrencyIds, true)) {
                $result[] = new AverageCurrencyRateDto(
                    new Id($item['currencyId']),
                    $averageCurrencyRatesDto->baseCurrency->getId(),
                    new DecimalNumber($item['rate']),
                    $averageCurrencyRatesDto->periodStart,
                    $averageCurrencyRatesDto->periodEnd
                );
            }
        }

        return $result;
    }
}
