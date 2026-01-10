<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Repository;

use App\EconumoBundle\Domain\Entity\UserPasswordRequest;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\UserPasswordRequestCode;

interface UserPasswordRequestRepositoryInterface
{
    public function getNextIdentity(): Id;

    public function getByUserAndCode(Id $userId, UserPasswordRequestCode $code): UserPasswordRequest;

    public function getByUser(Id $userId): UserPasswordRequest;

    /**
     * @param UserPasswordRequest[] $items
     */
    public function save(array $items): void;

    public function removeUserCodes(Id $userId): void;

    public function delete(UserPasswordRequest $item): void;
}
