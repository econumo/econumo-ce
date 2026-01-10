<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Dto;

use App\EconumoBundle\Application\Budget\Dto\BudgeFiltersResultDto;
use App\EconumoBundle\Application\Budget\Dto\BudgetCurrencyBalanceResultDto;
use App\EconumoBundle\Application\Budget\Dto\BudgetMetaResultDto;
use App\EconumoBundle\Application\Budget\Dto\BudgetStructureResultDto;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"meta", "filters", "balances", "structure"}
 * )
 */
class BudgetResultDto
{
    /**
     * @OA\Property()
     */
    public BudgetMetaResultDto $meta;

    /**
     * @OA\Property()
     */
    public BudgeFiltersResultDto $filters;

    /**
     * @var BudgetCurrencyBalanceResultDto[]
     * @OA\Property()
     */
    public array $balances = [];

    /**
     * @var BudgetAverageCurrencyRateResultDto[]
     * @OA\Property()
     */
    public array $currencyRates = [];

    /**
     * @OA\Property()
     */
    public BudgetStructureResultDto $structure;
}