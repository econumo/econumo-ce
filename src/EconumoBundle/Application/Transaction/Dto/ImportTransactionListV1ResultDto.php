<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Transaction\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"imported", "skipped", "errors"}
 * )
 */
class ImportTransactionListV1ResultDto
{
    /**
     * Number of successfully imported transactions
     * @OA\Property(example=10)
     */
    public int $imported = 0;

    /**
     * Number of skipped transactions
     * @OA\Property(example=2)
     */
    public int $skipped = 0;

    /**
     * Error messages for failed imports
     * @OA\Property(
     *     type="object",
     *     additionalProperties=@OA\Schema(
     *         type="array",
     *         @OA\Items(type="integer")
     *     ),
     *     example={"Invalid account name": {3, 5}, "Invalid date format": {8}}
     * )
     */
    public array $errors = [];
}
