<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Currency\Assembler;

use App\EconumoBundle\Application\Currency\Dto\GetCurrencyListV1RequestDto;
use App\EconumoBundle\Application\Currency\Assembler\CurrencyToDtoV1ResultAssembler;
use App\EconumoBundle\Application\Currency\Dto\GetCurrencyListV1ResultDto;
use App\EconumoBundle\Domain\Entity\Currency;

class GetCurrencyListV1ResultAssembler
{
    public function __construct(private readonly CurrencyToDtoV1ResultAssembler $currencyToDtoV1ResultAssembler)
    {
    }

    /**
     * @param Currency[] $currencies
     */
    public function assemble(
        GetCurrencyListV1RequestDto $dto,
        array $currencies
    ): GetCurrencyListV1ResultDto {
        $result = new GetCurrencyListV1ResultDto();
        foreach ($currencies as $currency) {
            $result->items[] = $this->currencyToDtoV1ResultAssembler->assemble($currency);
        }

        return $result;
    }
}
