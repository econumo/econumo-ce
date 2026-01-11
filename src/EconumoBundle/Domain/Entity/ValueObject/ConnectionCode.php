<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Entity\ValueObject;

use App\EconumoBundle\Domain\Entity\ValueObject\ValueObjectInterface;
use App\EconumoBundle\Domain\Exception\DomainException;
use App\EconumoBundle\Domain\Traits\ValueObjectTrait;
use JsonSerializable;

class ConnectionCode implements ValueObjectInterface, JsonSerializable
{
    use ValueObjectTrait;

    /**
     * @var int
     */
    private const LENGTH = 5;

    public static function validate($value): void
    {
        if (!is_string($value)) {
            throw new DomainException('ConnectionCode is incorrect');
        }

        $length = mb_strlen($value);
        if ($length !== self::LENGTH) {
            throw new DomainException('ConnectionCode is incorrect');
        }
    }

    public static function generate(): self
    {
        $code = substr(md5(uniqid('', true)), 0, self::LENGTH);
        $result = '';
        for ($i = 0; $i < strlen($code); ++$i) {
            $result .= (random_int(0, 1) === 1 ? strtoupper($code[$i]) : $code[$i]);
        }

        return new self($result);
    }
}
