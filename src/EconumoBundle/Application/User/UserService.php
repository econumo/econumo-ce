<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\User;

use App\EconumoBundle\Application\Exception\ValidationException;
use App\EconumoBundle\Application\User\Dto\LoginUserV1ResultDto;
use App\EconumoBundle\Application\User\Assembler\LoginUserV1ResultAssembler;
use App\EconumoBundle\Application\User\Dto\LogoutUserV1RequestDto;
use App\EconumoBundle\Application\User\Dto\LogoutUserV1ResultDto;
use App\EconumoBundle\Application\User\Assembler\LogoutUserV1ResultAssembler;
use App\EconumoBundle\Domain\Entity\User;
use App\EconumoBundle\Domain\Entity\ValueObject\Email;
use App\EconumoBundle\Domain\Exception\UserRegisteredException;
use App\EconumoBundle\Domain\Exception\UserRegistrationDisabledException;
use App\EconumoBundle\Domain\Service\Translation\TranslationServiceInterface;
use App\EconumoBundle\Domain\Service\User\UserRegistrationServiceInterface;
use App\EconumoBundle\Domain\Service\UserServiceInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use App\EconumoBundle\Application\User\Dto\RegisterUserV1RequestDto;
use App\EconumoBundle\Application\User\Dto\RegisterUserV1ResultDto;
use App\EconumoBundle\Application\User\Assembler\RegisterUserV1ResultAssembler;

readonly class UserService
{
    public function __construct(
        private LoginUserV1ResultAssembler $loginUserV1ResultAssembler,
        private LogoutUserV1ResultAssembler $logoutUserV1ResultAssembler,
        private JWTTokenManagerInterface $authToken,
        private RegisterUserV1ResultAssembler $registerUserV1ResultAssembler,
        private UserServiceInterface $userService,
        private TranslationServiceInterface $translationService,
        private UserRegistrationServiceInterface $userRegistrationService
    ) {
    }

    public function loginUser(
        User $user
    ): LoginUserV1ResultDto {
        $token = $this->authToken->create($user);
        return $this->loginUserV1ResultAssembler->assemble($user, $token);
    }

    public function logoutUser(
        string $token
    ): LogoutUserV1ResultDto {
        return $this->logoutUserV1ResultAssembler->assemble(new LogoutUserV1RequestDto());
    }

    public function registerUser(
        RegisterUserV1RequestDto $dto
    ): RegisterUserV1ResultDto {
        try {
            if (!$this->userRegistrationService->isRegistrationAllowed()) {
                throw new UserRegistrationDisabledException();
            }

            $user = $this->userService->register(new Email($dto->email), $dto->password, $dto->name);
            return $this->registerUserV1ResultAssembler->assemble($dto, $user);
        } catch (UserRegisteredException $userRegisteredException) {
            throw new ValidationException(
                $this->translationService->trans('user.user.already_exists'),
                400,
                $userRegisteredException
            );
        } catch (UserRegistrationDisabledException $userRegistrationDisabledException) {
            throw new ValidationException('Registration disabled', 400, $userRegistrationDisabledException);
        }
    }
}
