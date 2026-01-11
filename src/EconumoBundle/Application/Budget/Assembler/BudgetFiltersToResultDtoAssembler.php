<?php

declare(strict_types=1);


namespace App\EconumoBundle\Application\Budget\Assembler;

use App\EconumoBundle\Application\Budget\Dto\BudgeFiltersResultDto;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetFiltersDto;

readonly class BudgetFiltersToResultDtoAssembler
{
    public function assemble(BudgetFiltersDto $dto): BudgeFiltersResultDto
    {
        $result = new BudgeFiltersResultDto();
        $result->periodStart = $dto->periodStart->format('Y-m-d H:i:s');
        $result->periodEnd = $dto->periodEnd->format('Y-m-d H:i:s');
        $result->excludedAccountsIds = [];
        foreach ($dto->excludedAccountsIds as $excludedAccountId) {
            $result->excludedAccountsIds[] = $excludedAccountId->getValue();
        }

        return $result;
    }
}
