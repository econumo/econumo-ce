<?php

declare(strict_types=1);


namespace App\EconumoBundle\Application\Budget\Assembler;

use App\EconumoBundle\Application\Budget\Dto\BudgetCurrencyAmountDto;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetElementAmountSpentDto;

readonly class BudgetCurrencyAmountToResultDtoAssembler
{
    public function assemble(BudgetElementAmountSpentDto $dto): BudgetCurrencyAmountDto
    {
        $result = new BudgetCurrencyAmountDto();
        $result->currencyId = $dto->currencyId->getValue();
        $result->amount = $dto->amount->getValue();

        return $result;
    }
}
