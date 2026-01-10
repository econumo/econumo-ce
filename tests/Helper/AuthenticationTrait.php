<?php

declare(strict_types=1);


namespace App\Tests\Helper;


use App\EconumoBundle\Domain\Entity\ValueObject\Email;
use App\EconumoBundle\Domain\Repository\UserRepositoryInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

trait AuthenticationTrait
{
    use ContainerTrait;

    public function amAuthenticatedAsJohn(): void
    {
        $this->amAuthenticatedAsUser('john@econumo.test');
    }

    public function amAuthenticatedAsDany(): void
    {
        $this->amAuthenticatedAsUser('dany@econumo.test');
    }

    public function amAuthenticatedAsSansa(): void
    {
        $this->amAuthenticatedAsUser('sansa@econumo.test');
    }

    public function amAuthenticatedAsSystemUser(): void
    {
        /** @var \Codeception\Module\REST $rest */
        $rest = $this->getModule('REST');
        $rest->haveHttpHeader('Authorization', 'test');
    }

    public function amAuthenticatedAsUser(string $email): void
    {
        /** @var UserRepositoryInterface $userRepository */
        $userRepository = $this->getContainerService(UserRepositoryInterface::class);
        $user = $userRepository->getByEmail(new Email($email));
        /** @var JWTTokenManagerInterface $tokenManager */
        $tokenManager = $this->getContainerService(JWTTokenManagerInterface::class);
        $token = $tokenManager->create($user);
        /** @var \Codeception\Module\REST $rest */
        $rest = $this->getModule('REST');
        $rest->amBearerAuthenticated($token);
    }

}
