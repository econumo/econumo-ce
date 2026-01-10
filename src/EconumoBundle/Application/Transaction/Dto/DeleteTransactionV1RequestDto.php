<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Transaction\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"id"}
 * )
 */
class DeleteTransactionV1RequestDto
{
    /**
     * @OA\Property(example="123")
     */
    public string $id;
}
