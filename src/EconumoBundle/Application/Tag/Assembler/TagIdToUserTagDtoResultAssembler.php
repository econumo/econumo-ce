<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Tag\Assembler;

use App\EconumoBundle\Application\Tag\Dto\TagResultDto;
use App\EconumoBundle\Application\Tag\Assembler\TagToUserTagDtoResultAssembler;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Repository\TagRepositoryInterface;

readonly class TagIdToUserTagDtoResultAssembler
{
    public function __construct(private TagRepositoryInterface $tagRepository, private TagToUserTagDtoResultAssembler $tagToDtoResultAssembler)
    {
    }

    public function assemble(Id $tagId): TagResultDto
    {
        $tag = $this->tagRepository->get($tagId);
        return $this->tagToDtoResultAssembler->assemble($tag);
    }
}
