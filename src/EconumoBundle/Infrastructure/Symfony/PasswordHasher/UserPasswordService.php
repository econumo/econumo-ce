<?php

declare(strict_types=1);


namespace App\EconumoBundle\Infrastructure\Symfony\PasswordHasher;


use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Exception\UserPasswordNotValidException;
use App\EconumoBundle\Domain\Repository\UserRepositoryInterface;
use App\EconumoBundle\Domain\Service\User\UserPasswordServiceInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPasswordService implements UserPasswordServiceInterface
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher, private readonly UserRepositoryInterface $userRepository)
    {
    }

    public function changePassword(Id $userId, string $oldPassword, string $newPassword): void
    {
        $user = $this->userRepository->get($userId);
        if (!$this->passwordHasher->isPasswordValid($user, $oldPassword)) {
            throw new UserPasswordNotValidException();
        }

        $this->updatePassword($userId, $newPassword);
    }

    public function updatePassword(Id $userId, string $password): void
    {
        $user = $this->userRepository->get($userId);
        $user->updatePassword($this->passwordHasher->hashPassword($user, $password));

        $this->userRepository->save([$user]);
    }
}
