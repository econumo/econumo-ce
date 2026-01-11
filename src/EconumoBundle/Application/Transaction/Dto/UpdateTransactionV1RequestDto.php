<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Transaction\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"id", "type", "amount", "accountId", "date"}
 * )
 */
class UpdateTransactionV1RequestDto
{
    /**
     * @OA\Property(example="fa725671-bdce-43e6-8159-f37b748a667f")
     */
    public string $id;

    /**
     * @OA\Property(example="expense")
     */
    public string $type;

    /**
     * @OA\Property(example="1234.4")
     */
    public float|string $amount;

    /**
     * @OA\Property(example="1234.4")
     */
    public float|string|null $amountRecipient = null;

    /**
     * @OA\Property(example="")
     */
    public string $accountId;

    /**
     * @OA\Property(example="")
     */
    public ?string $accountRecipientId = null;

    /**
     * @OA\Property(example="")
     */
    public ?string $categoryId = null;

    /**
     * @OA\Property(example="2021-07-22 00:22:00")
     */
    public string $date;

    /**
     * @OA\Property(example="")
     */
    public ?string $description = null;

    /**
     * @OA\Property(example="")
     */
    public ?string $payeeId = null;

    /**
     * @OA\Property(example="")
     */
    public ?string $tagId = null;
}
