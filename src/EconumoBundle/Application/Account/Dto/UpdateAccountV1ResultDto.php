<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Account\Dto;

use App\EconumoBundle\Application\Account\Dto\AccountResultDto;
use App\EconumoBundle\Application\Transaction\Dto\TransactionResultDto;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"item"}
 * )
 */
class UpdateAccountV1ResultDto
{
    /**
     * @OA\Property()
     */
    public AccountResultDto $item;

    /**
     * @OA\Property()
     */
    public ?TransactionResultDto $transaction = null;
}
