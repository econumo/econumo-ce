<?php
declare(strict_types=1);

namespace App\EconumoBundle\Domain\Repository;

use App\EconumoBundle\Domain\Entity\UserOption;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;

interface UserOptionRepositoryInterface
{
    public function getNextIdentity(): Id;

    /**
     * @param UserOption[] $userOptions
     */
    public function save(array $userOptions): void;

    public function delete(UserOption $userOption): void;

    /**
     * @return UserOption[]
     */
    public function findByUserId(Id $userId): array;

    public function getReference(Id $id): UserOption;
}
