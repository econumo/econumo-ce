<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Currency\Assembler;

use App\EconumoBundle\Application\Currency\Dto\CurrencyResultDto;
use App\EconumoBundle\Domain\Entity\Currency;

class CurrencyToDtoV1ResultAssembler
{
    public function assemble(Currency $currency): CurrencyResultDto
    {
        $dto = new CurrencyResultDto();
        $dto->id = $currency->getId()->getValue();
        $dto->code = $currency->getCode()->getValue();
        $dto->name = $currency->getName();
        $dto->symbol = $currency->getSymbol();
        $dto->fractionDigits = $currency->getFractionDigits();
        return $dto;
    }
}
