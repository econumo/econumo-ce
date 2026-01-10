<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Category\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"id", "mode"}
 * )
 */
class DeleteCategoryV1RequestDto
{
    /**
     * @var string
     */
    final public const MODE_DELETE = 'delete';

    /**
     * @var string
     */
    final public const MODE_REPLACE = 'replace';

    /**
     * @OA\Property(example="95587d1d-2c39-4efc-98f3-23c755da44a4")
     */
    public string $id;

    /**
     * @OA\Property(example="delete or replace")
     */
    public string $mode;

    /**
     * @OA\Property(example="ed547399-a380-43c9-b164-d8e435e043c9")
     */
    public ?string $replaceId = null;
}
