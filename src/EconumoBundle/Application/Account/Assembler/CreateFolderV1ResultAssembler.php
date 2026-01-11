<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Account\Assembler;

use App\EconumoBundle\Application\Account\Assembler\FolderToDtoV1ResultAssembler;
use App\EconumoBundle\Application\Account\Dto\CreateFolderV1RequestDto;
use App\EconumoBundle\Application\Account\Dto\CreateFolderV1ResultDto;
use App\EconumoBundle\Domain\Entity\Folder;

class CreateFolderV1ResultAssembler
{
    public function __construct(private readonly FolderToDtoV1ResultAssembler $folderToDtoV1ResultAssembler)
    {
    }

    public function assemble(
        CreateFolderV1RequestDto $dto,
        Folder $folder
    ): CreateFolderV1ResultDto {
        $result = new CreateFolderV1ResultDto();
        $result->item = $this->folderToDtoV1ResultAssembler->assemble($folder);

        return $result;
    }
}
