<?php
declare(strict_types=1);

namespace App\EconumoBundle\Domain\Factory;

use App\EconumoBundle\Domain\Entity\User;
use App\EconumoBundle\Domain\Entity\ValueObject\Email;

interface UserFactoryInterface
{
    public function create(string $name, Email $email, string $password): User;
}
