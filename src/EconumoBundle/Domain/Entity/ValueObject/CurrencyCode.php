<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Entity\ValueObject;

use App\EconumoBundle\Domain\Entity\ValueObject\ValueObjectInterface;
use App\EconumoBundle\Domain\Exception\DomainException;
use App\EconumoBundle\Domain\Traits\ValueObjectTrait;
use JsonSerializable;

class CurrencyCode implements ValueObjectInterface, JsonSerializable
{
    use ValueObjectTrait;

    /**
     * @var int
     */
    private const LENGTH = 3;

    public function __construct(string $value)
    {
        $value = trim(strtoupper($value));
        self::validate($value);
        $this->value = $value;
    }

    public static function validate($value): void
    {
        if (!is_string($value)) {
            throw new DomainException('CurrencyCode is incorrect');
        }

        $length = mb_strlen($value);
        if ($length !== self::LENGTH) {
            throw new DomainException('CurrencyCode is incorrect');
        }
    }
}
