<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service;

use Throwable;
use App\EconumoBundle\Domain\Entity\Folder;
use App\EconumoBundle\Domain\Entity\ValueObject\FolderName;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Exception\AccessDeniedException;
use App\EconumoBundle\Domain\Exception\FolderAlreadyExistsException;
use App\EconumoBundle\Domain\Exception\LastFolderRemoveException;
use App\EconumoBundle\Domain\Factory\FolderFactoryInterface;
use App\EconumoBundle\Domain\Repository\FolderRepositoryInterface;
use App\EconumoBundle\Domain\Service\AntiCorruptionServiceInterface;
use App\EconumoBundle\Domain\Service\Dto\PositionDto;
use App\EconumoBundle\Domain\Service\FolderServiceInterface;
use DateTimeInterface;

final readonly class FolderService implements FolderServiceInterface
{
    public function __construct(private FolderRepositoryInterface $folderRepository, private FolderFactoryInterface $folderFactory, private AntiCorruptionServiceInterface $antiCorruptionService)
    {
    }

    public function create(Id $userId, FolderName $name): Folder
    {
        $userFolders = $this->folderRepository->getByUserId($userId);
        $lastFolderPosition = 0;
        foreach ($userFolders as $userFolder) {
            if ($userFolder->getName()->isEqual($name)) {
                throw new FolderAlreadyExistsException();
            }

            if ($userFolder->getPosition() > $lastFolderPosition) {
                $lastFolderPosition = $userFolder->getPosition();
            }
        }

        $folder = $this->folderFactory->create($userId, $name);
        $folder->updatePosition($lastFolderPosition + 1);

        $this->folderRepository->save([$folder]);

        return $folder;
    }

    public function update(Id $folderId, FolderName $name): void
    {
        $folder = $this->folderRepository->get($folderId);
        $userFolders = $this->folderRepository->getByUserId($folder->getUserId());
        foreach ($userFolders as $userFolder) {
            if ($userFolder->getName()->isEqual($name)) {
                throw new FolderAlreadyExistsException();
            }
        }

        $folder->updateName($name);
        $this->folderRepository->save([$folder]);
    }

    public function delete(Id $folderId): void
    {
        $folder = $this->folderRepository->get($folderId);
        if (!$this->folderRepository->isUserHasMoreThanOneFolder($folder->getUserId())) {
            throw new LastFolderRemoveException();
        }

        $this->antiCorruptionService->beginTransaction(__METHOD__);
        try {
            $userId = $folder->getUserId();
            $this->folderRepository->delete($folder);
            $this->resetOrderFolders($userId);
            $this->antiCorruptionService->commit(__METHOD__);
        } catch (Throwable $throwable) {
            $this->antiCorruptionService->rollback(__METHOD__);
            throw $throwable;
        }
    }

    public function replace(Id $folderId, Id $replaceFolderId): void
    {
        $folder = $this->folderRepository->get($folderId);
        $replaceFolder = $this->folderRepository->get($replaceFolderId);
        if (!$folder->getUserId()->isEqual($replaceFolder->getUserId())) {
            throw new AccessDeniedException();
        }

        $userId = $folder->getUserId();

        $this->antiCorruptionService->beginTransaction(__METHOD__);
        try {
            foreach ($folder->getAccounts() as $account) {
                if (!$replaceFolder->containsAccount($account)) {
                    $replaceFolder->addAccount($account);
                }
            }

            $this->folderRepository->delete($folder);
            $this->folderRepository->save([$replaceFolder]);

            $this->resetOrderFolders($userId);
            $this->antiCorruptionService->commit(__METHOD__);
        } catch (Throwable $throwable) {
            $this->antiCorruptionService->rollback(__METHOD__);
            throw $throwable;
        }
    }

    /**
     * @inheritDoc
     */
    public function orderFolders(Id $userId, array $changes): void
    {
        $folders = $this->folderRepository->getByUserId($userId);
        $changed = [];
        foreach ($folders as $folder) {
            foreach ($changes as $change) {
                if ($folder->getId()->isEqual($change->getId())) {
                    $folder->updatePosition($change->position);
                    $changed[] = $folder;
                    break;
                }
            }
        }

        if ($changed === []) {
            return;
        }

        $this->folderRepository->save($changed);
    }

    private function resetOrderFolders(Id $userId): void
    {
        $userFolders = $this->folderRepository->getByUserId($userId);
        usort($userFolders, static fn(Folder $a, Folder $b): int => $a->getPosition() <=> $b->getPosition());
        foreach ($userFolders as $i => $userFolder) {
            $userFolder->updatePosition($i);
        }

        $this->folderRepository->save($userFolders);
    }

    public function hide(Id $folderId): void
    {
        $folder = $this->folderRepository->get($folderId);
        $folder->makeInvisible();

        $this->folderRepository->save([$folder]);
    }

    public function show(Id $folderId): void
    {
        $folder = $this->folderRepository->get($folderId);
        $folder->makeVisible();

        $this->folderRepository->save([$folder]);
    }

    public function getChanged(Id $userId, DateTimeInterface $lastUpdate): array
    {
        $folders = $this->folderRepository->getByUserId($userId);
        $result = [];
        foreach ($folders as $folder) {
            if ($folder->getUpdatedAt() > $lastUpdate) {
                $result[] = $folder;
            }
        }

        return $result;
    }
}
