<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Entity\ValueObject;

use App\EconumoBundle\Domain\Entity\ValueObject\ValueObjectInterface;
use App\EconumoBundle\Domain\Exception\DomainException;
use App\EconumoBundle\Domain\Traits\ValueObjectTrait;
use JsonSerializable;

class ReportPeriod implements ValueObjectInterface, JsonSerializable
{
    use ValueObjectTrait;

    /**
     * @var string
     */
    final public const MONTHLY = 'monthly';

    /**
     * @var string[]
     */
    private const OPTIONS = [
        self::MONTHLY
    ];

    public static function validate($value): void
    {
        if (!in_array($value, self::OPTIONS, true)) {
            throw new DomainException('ReportPeriod is incorrect');
        }
    }
}
