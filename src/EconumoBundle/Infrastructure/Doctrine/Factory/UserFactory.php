<?php

declare(strict_types=1);

namespace App\EconumoBundle\Infrastructure\Doctrine\Factory;

use App\EconumoBundle\Domain\Entity\User;
use App\EconumoBundle\Domain\Entity\ValueObject\Email;
use App\EconumoBundle\Domain\Factory\UserFactoryInterface;
use App\EconumoBundle\Domain\Repository\UserRepositoryInterface;
use App\EconumoBundle\Domain\Service\DatetimeServiceInterface;
use App\EconumoBundle\Domain\Service\EncodeServiceInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class UserFactory implements UserFactoryInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private DatetimeServiceInterface $datetimeService,
        private UserPasswordHasherInterface $userPasswordHasher,
        private EncodeServiceInterface $encodeService
    ) {
    }

    public function create(string $name, Email $email, string $password): User
    {
        $emailValue = strtolower($email->getValue());
        $identifier = $this->encodeService->hash($emailValue);
        $encodedEmail = $this->encodeService->encode($email->getValue());
        $avatarUrl = sprintf('https://www.gravatar.com/avatar/%s', md5($emailValue));
        $user = new User(
            $this->userRepository->getNextIdentity(),
            $identifier,
            sha1(random_bytes(10)),
            $encodedEmail,
            $name,
            $avatarUrl,
            $this->datetimeService->getCurrentDatetime()
        );
        $user->updatePassword($this->userPasswordHasher->hashPassword($user, $password));

        return $user;
    }
}
