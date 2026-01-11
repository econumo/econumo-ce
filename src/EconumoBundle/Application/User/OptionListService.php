<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\User;

use App\EconumoBundle\Application\User\Dto\GetOptionListV1RequestDto;
use App\EconumoBundle\Application\User\Dto\GetOptionListV1ResultDto;
use App\EconumoBundle\Application\User\Assembler\GetOptionListV1ResultAssembler;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Repository\UserOptionRepositoryInterface;

class OptionListService
{
    public function __construct(private readonly GetOptionListV1ResultAssembler $getOptionListV1ResultAssembler, private readonly UserOptionRepositoryInterface $userOptionRepository)
    {
    }

    public function getOptionList(
        GetOptionListV1RequestDto $dto,
        Id $userId
    ): GetOptionListV1ResultDto {
        $options = $this->userOptionRepository->findByUserId($userId);
        return $this->getOptionListV1ResultAssembler->assemble($dto, $options);
    }
}
