<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Repository;

use App\EconumoBundle\Domain\Entity\Folder;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;

interface FolderRepositoryInterface
{
    public function getNextIdentity(): Id;

    /**
     * @return Folder[]
     */
    public function getByUserId(Id $userId): array;

    public function getLastFolder(Id $userId): Folder;

    public function get(Id $id): Folder;

    /**
     * @param Folder[] $items
     */
    public function save(array $items): void;

    public function delete(Folder $folder): void;

    public function isUserHasMoreThanOneFolder(Id $userId): bool;
}
