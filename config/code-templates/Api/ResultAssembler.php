<?php

declare(strict_types=1);

namespace _CG_APPROOT_\Application\_CG_MODULE_\Assembler;

use _CG_APPROOT_\Application\_CG_MODULE_\Dto\_CG_ACTION__CG_SUBJECT__CG_VERSION_RequestDto;
use _CG_APPROOT_\Application\_CG_MODULE_\Dto\_CG_ACTION__CG_SUBJECT__CG_VERSION_ResultDto;

readonly class _CG_ACTION__CG_SUBJECT__CG_VERSION_ResultAssembler
{
    public function assemble(
        _CG_ACTION__CG_SUBJECT__CG_VERSION_RequestDto $dto
    ): _CG_ACTION__CG_SUBJECT__CG_VERSION_ResultDto {
        $result = new _CG_ACTION__CG_SUBJECT__CG_VERSION_ResultDto();
        $result->result = 'test';

        return $result;
    }
}
