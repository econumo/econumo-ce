<?php

declare(strict_types=1);

namespace _CG_APPROOT_\Application\_CG_MODULE_\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"result"}
 * )
 */
class _CG_ACTION__CG_SUBJECT__CG_VERSION_ResultDto
{
    /**
     * Id
     * @OA\Property(example="This is result")
     */
    public string $result;
}
