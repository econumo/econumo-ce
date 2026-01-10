<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Tag;

use App\EconumoBundle\Application\Exception\ValidationException;
use App\EconumoBundle\Application\Tag\Assembler\GetTagListV1ResultAssembler;
use App\EconumoBundle\Application\Tag\Dto\GetTagListV1RequestDto;
use App\EconumoBundle\Application\Tag\Dto\GetTagListV1ResultDto;
use App\EconumoBundle\Application\Tag\Dto\OrderTagListV1RequestDto;
use App\EconumoBundle\Application\Tag\Dto\OrderTagListV1ResultDto;
use App\EconumoBundle\Application\Tag\Assembler\OrderTagListV1ResultAssembler;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Repository\TagRepositoryInterface;
use App\EconumoBundle\Domain\Service\TagServiceInterface;
use App\EconumoBundle\Domain\Service\Translation\TranslationServiceInterface;

class TagListService
{
    public function __construct(private readonly GetTagListV1ResultAssembler $getTagListV1ResultAssembler, private readonly TagRepositoryInterface $tagRepository, private readonly OrderTagListV1ResultAssembler $orderTagListV1ResultAssembler, private readonly TagServiceInterface $tagService, private readonly TranslationServiceInterface $translationService)
    {
    }

    public function getTagList(
        GetTagListV1RequestDto $dto,
        Id $userId
    ): GetTagListV1ResultDto {
        $tags = $this->tagRepository->findAvailableForUserId($userId);
        return $this->getTagListV1ResultAssembler->assemble($dto, $tags);
    }

    public function orderTagList(
        OrderTagListV1RequestDto $dto,
        Id $userId
    ): OrderTagListV1ResultDto {
        if ($dto->changes === []) {
            throw new ValidationException($this->translationService->trans('tag.tag_list.empty_list'));
        }

        $this->tagService->orderTags($userId, $dto->changes);

        return $this->orderTagListV1ResultAssembler->assemble($dto, $userId);
    }
}
