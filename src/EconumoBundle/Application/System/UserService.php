<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\System;

use App\EconumoBundle\Application\Exception\ValidationException;
use App\EconumoBundle\Application\System\Dto\CreateUserV1RequestDto;
use App\EconumoBundle\Application\System\Dto\CreateUserV1ResultDto;
use App\EconumoBundle\Application\System\Assembler\CreateUserV1ResultAssembler;
use App\EconumoBundle\Domain\Entity\ValueObject\Email;
use App\EconumoBundle\Domain\Exception\UserRegisteredException;
use App\EconumoBundle\Domain\Service\EconumoServiceInterface;
use App\EconumoBundle\Domain\Service\EmailServiceInterface;
use App\EconumoBundle\Domain\Service\Translation\TranslationServiceInterface;
use App\EconumoBundle\Domain\Service\UserServiceInterface;

readonly class UserService
{
    public function __construct(
        private CreateUserV1ResultAssembler $createUserV1ResultAssembler,
        private UserServiceInterface $userService,
        private EmailServiceInterface $emailService,
        private TranslationServiceInterface $translationService,
        private EconumoServiceInterface $econumoService
    ) {
    }

    public function createUser(
        string $baseUrl,
        CreateUserV1RequestDto $dto
    ): CreateUserV1ResultDto {
        $email = new Email($dto->email);
        $password = $dto->password;
        $name = $dto->name;
        try {
            $user = $this->userService->register($email, $password, $name);
            $baseUrl = empty($this->econumoService->getBaseUrl()) ? $baseUrl : $this->econumoService->getBaseUrl();
            $this->emailService->sendWelcomeEmailWithPassword($email, $password, $baseUrl);
        } catch (UserRegisteredException $userRegisteredException) {
            throw new ValidationException(
                $this->translationService->trans('user.user.already_exists'),
                400,
                $userRegisteredException
            );
        }

        return $this->createUserV1ResultAssembler->assemble($user);
    }
}
