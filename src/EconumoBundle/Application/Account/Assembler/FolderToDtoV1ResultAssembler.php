<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Account\Assembler;

use App\EconumoBundle\Application\Account\Dto\FolderResultDto;
use App\EconumoBundle\Domain\Entity\Folder;

class FolderToDtoV1ResultAssembler
{
    public function assemble(Folder $folder): FolderResultDto
    {
        $item = new FolderResultDto();
        $item->id = $folder->getId()->getValue();
        $item->name = $folder->getName()->getValue();
        $item->position = $folder->getPosition();
        $item->isVisible = (int)$folder->isVisible();

        return $item;
    }
}
