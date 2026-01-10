<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service;


use App\EconumoBundle\Domain\Entity\ValueObject\Email;
use App\EconumoBundle\Domain\Entity\ValueObject\UserPasswordRequestCode;

interface PasswordUserReminderServiceInterface
{
    public function remindPassword(Email $email): void;

    public function resetPassword(
        Email $email,
        UserPasswordRequestCode $code,
        string $password
    ): void;
}
