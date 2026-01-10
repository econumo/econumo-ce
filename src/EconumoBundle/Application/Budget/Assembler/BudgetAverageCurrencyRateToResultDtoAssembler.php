<?php

declare(strict_types=1);


namespace App\EconumoBundle\Application\Budget\Assembler;

use App\EconumoBundle\Application\Budget\Dto\BudgetAverageCurrencyRateResultDto;
use App\EconumoBundle\Domain\Service\Budget\Dto\AverageCurrencyRateDto;

readonly class BudgetAverageCurrencyRateToResultDtoAssembler
{
    public function assemble(AverageCurrencyRateDto $dto): BudgetAverageCurrencyRateResultDto
    {
        $result = new BudgetAverageCurrencyRateResultDto();
        $result->currencyId = $dto->currencyId->getValue();
        $result->baseCurrencyId = $dto->baseCurrencyId->getValue();
        $result->rate = $dto->value->getValue();
        $result->periodStart = $dto->periodStart->format('Y-m-d');
        $result->periodEnd = $dto->periodEnd->format('Y-m-d');

        return $result;
    }
}
