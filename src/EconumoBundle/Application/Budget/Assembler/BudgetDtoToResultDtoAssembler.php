<?php

declare(strict_types=1);


namespace App\EconumoBundle\Application\Budget\Assembler;


use App\EconumoBundle\Application\Budget\Assembler\BudgetCurrencyBalanceToResultDtoAssembler;
use App\EconumoBundle\Application\Budget\Assembler\BudgetFiltersToResultDtoAssembler;
use App\EconumoBundle\Application\Budget\Assembler\BudgetStructureToResultDtoAssembler;
use App\EconumoBundle\Application\Budget\Dto\BudgetResultDto;
use App\EconumoBundle\Application\Budget\Assembler\BudgetMetaToResultDtoAssembler;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetDto;

readonly class BudgetDtoToResultDtoAssembler
{
    public function __construct(
        private BudgetMetaToResultDtoAssembler $budgetMetaToResultDtoAssembler,
        private BudgetStructureToResultDtoAssembler $budgetStructureToResultDtoAssembler,
        private BudgetCurrencyBalanceToResultDtoAssembler $budgetCurrencyBalanceToResultDtoAssembler,
        private BudgetFiltersToResultDtoAssembler $budgetFiltersToResultDtoAssembler,
        private BudgetAverageCurrencyRateToResultDtoAssembler $averageCurrencyRateToResultDtoAssembler
    ) {
    }

    public function assemble(BudgetDto $dto): BudgetResultDto
    {
        $result = new BudgetResultDto();
        $result->meta = $this->budgetMetaToResultDtoAssembler->assemble($dto->meta);
        $result->filters = $this->budgetFiltersToResultDtoAssembler->assemble($dto->filters);
        $result->balances = [];
        foreach ($dto->financialSummary->currencyBalances as $balance) {
            $result->balances[] = $this->budgetCurrencyBalanceToResultDtoAssembler->assemble($balance);
        }

        $result->currencyRates = [];
        foreach ($dto->financialSummary->averageCurrencyRates as $currencyRate) {
            $result->currencyRates[] = $this->averageCurrencyRateToResultDtoAssembler->assemble($currencyRate);
        }

        $result->structure = $this->budgetStructureToResultDtoAssembler->assemble($dto->structure);

        return $result;
    }
}
