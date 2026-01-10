<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"id", "name", "icon"}
 * )
 */
class BudgetTransactionCategoryResultDto
{
    /**
     * Id
     * @OA\Property(example="1b8559ac-4c77-47e4-a95c-1530a5274ab7")
     */
    public string $id;

    /**
     * Name
     * @OA\Property(example="bananas")
     */
    public string $name;

    /**
     * Icon
     * @OA\Property(example="bananas")
     */
    public string $icon;
}
