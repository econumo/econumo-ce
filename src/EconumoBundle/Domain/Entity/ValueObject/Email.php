<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Entity\ValueObject;

use Stringable;
use App\EconumoBundle\Domain\Exception\DomainException;

final class Email implements Stringable
{
    private string $value;

    public function __construct(string $email)
    {
        $email = mb_strtolower($email);
        if (false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new DomainException(sprintf('E-mail %s is not valid', $email));
        }

        $this->value = $email;
    }

    public function __toString(): string
    {
        return $this->getValue();
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
