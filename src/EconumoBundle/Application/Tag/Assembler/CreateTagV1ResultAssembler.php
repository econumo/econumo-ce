<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Tag\Assembler;

use App\EconumoBundle\Application\Tag\Assembler\TagToUserTagDtoResultAssembler;
use App\EconumoBundle\Application\Tag\Dto\CreateTagV1RequestDto;
use App\EconumoBundle\Application\Tag\Dto\CreateTagV1ResultDto;
use App\EconumoBundle\Domain\Entity\Tag;

class CreateTagV1ResultAssembler
{
    public function __construct(private readonly TagToUserTagDtoResultAssembler $tagToDtoV1ResultAssembler)
    {
    }

    public function assemble(
        CreateTagV1RequestDto $dto,
        Tag $tag
    ): CreateTagV1ResultDto {
        $result = new CreateTagV1ResultDto();
        $result->item = $this->tagToDtoV1ResultAssembler->assemble($tag);

        return $result;
    }
}
