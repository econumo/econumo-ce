<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Factory;

use App\EconumoBundle\Domain\Entity\Folder;
use App\EconumoBundle\Domain\Entity\ValueObject\FolderName;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Factory\FolderFactoryInterface;
use App\EconumoBundle\Domain\Repository\FolderRepositoryInterface;
use App\EconumoBundle\Domain\Repository\UserRepositoryInterface;
use App\EconumoBundle\Domain\Service\DatetimeServiceInterface;

class FolderFactory implements FolderFactoryInterface
{
    public function __construct(private readonly DatetimeServiceInterface $datetimeService, private readonly FolderRepositoryInterface $folderRepository, private readonly UserRepositoryInterface $userRepository)
    {
    }

    public function create(Id $userId, FolderName $name): Folder
    {
        return new Folder(
            $this->folderRepository->getNextIdentity(),
            $this->userRepository->getReference($userId),
            $name,
            $this->datetimeService->getCurrentDatetime()
        );
    }
}
