<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service\User;


interface UserRegistrationServiceInterface
{
    public function isRegistrationAllowed(): bool;
}
