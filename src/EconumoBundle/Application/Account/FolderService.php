<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Account;

use App\EconumoBundle\Application\Account\Assembler\CreateFolderV1ResultAssembler;
use App\EconumoBundle\Application\Account\Assembler\HideFolderV1ResultAssembler;
use App\EconumoBundle\Application\Account\Assembler\ReplaceFolderV1ResultAssembler;
use App\EconumoBundle\Application\Account\Assembler\UpdateFolderV1ResultAssembler;
use App\EconumoBundle\Application\Account\Dto\CreateFolderV1RequestDto;
use App\EconumoBundle\Application\Account\Dto\CreateFolderV1ResultDto;
use App\EconumoBundle\Application\Account\Dto\HideFolderV1RequestDto;
use App\EconumoBundle\Application\Account\Dto\HideFolderV1ResultDto;
use App\EconumoBundle\Application\Account\Dto\ReplaceFolderV1RequestDto;
use App\EconumoBundle\Application\Account\Dto\ReplaceFolderV1ResultDto;
use App\EconumoBundle\Application\Account\Dto\ShowFolderV1RequestDto;
use App\EconumoBundle\Application\Account\Dto\ShowFolderV1ResultDto;
use App\EconumoBundle\Application\Account\Assembler\ShowFolderV1ResultAssembler;
use App\EconumoBundle\Application\Account\Dto\UpdateFolderV1RequestDto;
use App\EconumoBundle\Application\Account\Dto\UpdateFolderV1ResultDto;
use App\EconumoBundle\Application\Exception\AccessDeniedException;
use App\EconumoBundle\Domain\Entity\ValueObject\FolderName;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Repository\FolderRepositoryInterface;
use App\EconumoBundle\Domain\Service\FolderServiceInterface;

class FolderService
{
    public function __construct(private readonly FolderServiceInterface $folderService, private readonly CreateFolderV1ResultAssembler $createFolderV1ResultAssembler, private readonly UpdateFolderV1ResultAssembler $updateFolderV1ResultAssembler, private readonly FolderRepositoryInterface $folderRepository, private readonly ReplaceFolderV1ResultAssembler $replaceFolderV1ResultAssembler, private readonly HideFolderV1ResultAssembler $hideFolderV1ResultAssembler, private readonly ShowFolderV1ResultAssembler $showFolderV1ResultAssembler)
    {
    }

    public function createFolder(
        CreateFolderV1RequestDto $dto,
        Id $userId
    ): CreateFolderV1ResultDto {
        $folder = $this->folderService->create($userId, new FolderName($dto->name));
        return $this->createFolderV1ResultAssembler->assemble($dto, $folder);
    }

    public function updateFolder(
        UpdateFolderV1RequestDto $dto,
        Id $userId
    ): UpdateFolderV1ResultDto {
        $folder = $this->folderRepository->get(new Id($dto->id));
        if (!$folder->getUserId()->isEqual($userId)) {
            throw new AccessDeniedException();
        }

        $this->folderService->update($folder->getId(), new FolderName($dto->name));
        return $this->updateFolderV1ResultAssembler->assemble($dto, $folder->getId());
    }

    public function replaceFolder(
        ReplaceFolderV1RequestDto $dto,
        Id $userId
    ): ReplaceFolderV1ResultDto {
        $folder = $this->folderRepository->get(new Id($dto->id));
        if (!$folder->getUserId()->isEqual($userId)) {
            throw new AccessDeniedException();
        }

        $this->folderService->replace($folder->getId(), new Id($dto->replaceId));
        return $this->replaceFolderV1ResultAssembler->assemble($dto);
    }

    public function hideFolder(
        HideFolderV1RequestDto $dto,
        Id $userId
    ): HideFolderV1ResultDto {
        $folder = $this->folderRepository->get(new Id($dto->id));
        if (!$folder->getUserId()->isEqual($userId)) {
            throw new AccessDeniedException();
        }

        $this->folderService->hide($folder->getId());
        return $this->hideFolderV1ResultAssembler->assemble($dto);
    }

    public function showFolder(
        ShowFolderV1RequestDto $dto,
        Id $userId
    ): ShowFolderV1ResultDto {
        $folder = $this->folderRepository->get(new Id($dto->id));
        if (!$folder->getUserId()->isEqual($userId)) {
            throw new AccessDeniedException();
        }

        $this->folderService->show($folder->getId());
        return $this->showFolderV1ResultAssembler->assemble($dto);
    }
}
