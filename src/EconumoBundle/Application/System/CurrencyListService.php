<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\System;

use App\EconumoBundle\Application\System\Dto\ImportCurrencyListV1RequestDto;
use App\EconumoBundle\Application\System\Dto\ImportCurrencyListV1ResultDto;
use App\EconumoBundle\Application\System\Assembler\ImportCurrencyListV1ResultAssembler;
use App\EconumoBundle\Domain\Entity\ValueObject\CurrencyCode;
use App\EconumoBundle\Domain\Service\Currency\CurrencyUpdateServiceInterface;
use App\EconumoBundle\Domain\Service\Dto\CurrencyDto;

readonly class CurrencyListService
{
    public function __construct(
        private ImportCurrencyListV1ResultAssembler $importCurrencyListV1ResultAssembler,
        private CurrencyUpdateServiceInterface $currencyUpdateService
    ) {
    }

    public function importCurrencyList(
        ImportCurrencyListV1RequestDto $dto
    ): ImportCurrencyListV1ResultDto {
        $currencies = [];
        foreach ($dto->items as $item) {
            $currencyDto = new CurrencyDto();
            $currencyDto->code = new CurrencyCode($item);
            $currencies[] = $currencyDto;
        }

        $this->currencyUpdateService->updateCurrencies($currencies);
        return $this->importCurrencyListV1ResultAssembler->assemble($dto);
    }
}
