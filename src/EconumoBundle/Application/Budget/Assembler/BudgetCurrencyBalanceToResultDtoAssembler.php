<?php

declare(strict_types=1);


namespace App\EconumoBundle\Application\Budget\Assembler;

use App\EconumoBundle\Application\Budget\Dto\BudgetCurrencyBalanceResultDto;
use App\EconumoBundle\Domain\Service\Budget\Dto\CurrencyBalanceDto;

readonly class BudgetCurrencyBalanceToResultDtoAssembler
{
    public function assemble(CurrencyBalanceDto $dto): BudgetCurrencyBalanceResultDto
    {
        $result = new BudgetCurrencyBalanceResultDto();
        $result->currencyId = $dto->currencyId->getValue();
        $result->startBalance = $dto->startBalance?->getValue();
        $result->endBalance = $dto->endBalance?->getValue();
        $result->income = $dto->income?->getValue();
        $result->expenses = $dto->expenses?->getValue();
        $result->exchanges = $dto->exchanges?->getValue();
        $result->holdings = $dto->holdings?->getValue();

        return $result;
    }
}
