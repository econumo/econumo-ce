<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"id", "accountId"}
 * )
 */
class ExcludeAccountV1RequestDto
{
    /**
     * Budget ID
     * @OA\Property(example="05c8f3e1-d77f-4b37-b2ca-0fc5f0f0c7a9")
     */
    public string $id;

    /**
     * @OA\Property(example="a5e2eee2-56aa-43c6-a827-ca155683ea8d")
     */
    public string $accountId;
}
