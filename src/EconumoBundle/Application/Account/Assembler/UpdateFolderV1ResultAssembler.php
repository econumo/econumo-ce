<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Account\Assembler;

use App\EconumoBundle\Application\Account\Dto\UpdateFolderV1RequestDto;
use App\EconumoBundle\Application\Account\Assembler\FolderIdToDtoV1ResultAssembler;
use App\EconumoBundle\Application\Account\Dto\UpdateFolderV1ResultDto;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;

class UpdateFolderV1ResultAssembler
{
    public function __construct(private readonly FolderIdToDtoV1ResultAssembler $folderIdToDtoV1ResultAssembler)
    {
    }

    public function assemble(
        UpdateFolderV1RequestDto $dto,
        Id $folderId
    ): UpdateFolderV1ResultDto {
        $result = new UpdateFolderV1ResultDto();
        $result->item = $this->folderIdToDtoV1ResultAssembler->assemble($folderId);

        return $result;
    }
}
