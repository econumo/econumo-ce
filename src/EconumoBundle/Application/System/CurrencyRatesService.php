<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\System;

use App\EconumoBundle\Application\System\Dto\ImportCurrencyRatesV1RequestDto;
use App\EconumoBundle\Application\System\Dto\ImportCurrencyRatesV1ResultDto;
use App\EconumoBundle\Application\System\Assembler\ImportCurrencyRatesV1ResultAssembler;
use App\EconumoBundle\Domain\Entity\ValueObject\CurrencyCode;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use App\EconumoBundle\Domain\Service\Currency\CurrencyRatesUpdateServiceInterface;
use App\EconumoBundle\Domain\Service\Dto\CurrencyRateDto;
use DateTimeImmutable;

readonly class CurrencyRatesService
{
    public function __construct(private ImportCurrencyRatesV1ResultAssembler $importCurrencyRatesV1ResultAssembler, private CurrencyRatesUpdateServiceInterface $currencyRatesUpdateService)
    {
    }

    public function importCurrencyRates(
        ImportCurrencyRatesV1RequestDto $dto
    ): ImportCurrencyRatesV1ResultDto {
        $currencyRatesDate = DateTimeImmutable::createFromFormat('U', $dto->timestamp);
        $currencyBase = new CurrencyCode($dto->base);
        $rates = [];
        foreach ($dto->items as $currencyRate) {
            $item = new CurrencyRateDto();
            $item->code = new CurrencyCode($currencyRate->code);
            $item->rate = new DecimalNumber($currencyRate->rate);
            $item->date = $currencyRatesDate;
            $item->base = $currencyBase;
            $rates[] = $item;
        }

        $this->currencyRatesUpdateService->updateCurrencyRates($rates);
        return $this->importCurrencyRatesV1ResultAssembler->assemble($dto);
    }
}
