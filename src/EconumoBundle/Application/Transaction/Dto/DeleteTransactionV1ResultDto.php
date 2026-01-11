<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Transaction\Dto;

use App\EconumoBundle\Application\Account\Dto\AccountResultDto;
use App\EconumoBundle\Application\Transaction\Dto\TransactionResultDto;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"item", "accounts"}
 * )
 */
class DeleteTransactionV1ResultDto
{
    /**
     * Transaction
     * @OA\Property()
     */
    public TransactionResultDto $item;

    /**
     * @var AccountResultDto[]
     * @OA\Property()
     */
    public array $accounts = [];
}
