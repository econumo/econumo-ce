<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service\User;

use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Exception\UserPasswordNotValidException;

interface UserPasswordServiceInterface
{
    public function updatePassword(Id $userId, string $password): void;

    /**
     * @throws UserPasswordNotValidException
     */
    public function changePassword(Id $userId, string $oldPassword, string $newPassword): void;
}
