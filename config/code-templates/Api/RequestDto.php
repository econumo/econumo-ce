<?php

declare(strict_types=1);

namespace _CG_APPROOT_\Application\_CG_MODULE_\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"id"}
 * )
 */
class _CG_ACTION__CG_SUBJECT__CG_VERSION_RequestDto
{
    /**
     * @OA\Property(example="123")
     */
    public string $id;
}
