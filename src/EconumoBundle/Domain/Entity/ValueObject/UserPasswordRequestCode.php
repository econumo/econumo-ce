<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Entity\ValueObject;

use App\EconumoBundle\Domain\Entity\ValueObject\ValueObjectInterface;
use App\EconumoBundle\Domain\Exception\DomainException;
use App\EconumoBundle\Domain\Traits\ValueObjectTrait;
use JsonSerializable;

class UserPasswordRequestCode implements ValueObjectInterface, JsonSerializable
{
    use ValueObjectTrait;

    /**
     * @var int
     */
    final public const LENGTH = 12;

    public static function validate($value): void
    {
        if (!is_string($value)) {
            throw new DomainException('UserPasswordRequestCode is incorrect');
        }

        $length = mb_strlen($value);
        if ($length !== self::LENGTH) {
            throw new DomainException('UserPasswordRequestCode is incorrect');
        }
    }

    public static function generate(): self
    {
        return new self(substr(md5(uniqid(UserPasswordRequestCode::class)), 0, self::LENGTH));
    }
}
