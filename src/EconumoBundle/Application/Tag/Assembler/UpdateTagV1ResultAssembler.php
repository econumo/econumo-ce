<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Tag\Assembler;

use App\EconumoBundle\Application\Tag\Dto\UpdateTagV1RequestDto;
use App\EconumoBundle\Application\Tag\Dto\UpdateTagV1ResultDto;
use App\EconumoBundle\Application\Tag\Assembler\TagIdToUserTagDtoResultAssembler;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;

class UpdateTagV1ResultAssembler
{
    public function __construct(private readonly TagIdToUserTagDtoResultAssembler $tagIdToDtoResultAssembler)
    {
    }

    public function assemble(
        UpdateTagV1RequestDto $dto
    ): UpdateTagV1ResultDto {
        $result = new UpdateTagV1ResultDto();
        $result->item = $this->tagIdToDtoResultAssembler->assemble(new Id($dto->id));

        return $result;
    }
}
