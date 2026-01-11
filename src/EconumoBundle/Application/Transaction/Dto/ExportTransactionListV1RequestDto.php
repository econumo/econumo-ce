<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Transaction\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema()
 */
class ExportTransactionListV1RequestDto
{
    /**
     * @OA\Property(example="2226af89-8272-4ba1-a42a-b56a4809e7b2,2855a07e-fc9a-41e6-91a1-b698a8740c22")
     */
    public ?string $accountId = null;
}
