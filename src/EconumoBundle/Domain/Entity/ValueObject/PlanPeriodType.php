<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Entity\ValueObject;

use Stringable;
use App\EconumoBundle\Domain\Exception\DomainException;
use App\EconumoBundle\Domain\Traits\ValueObjectTrait;
use JsonSerializable;

final class PlanPeriodType implements JsonSerializable, Stringable
{
    use ValueObjectTrait;

    /**
     * @var string
     */
    public const MONTHLY = 'month';

    /**
     * @var string[]
     */
    private const MAPPING = [self::MONTHLY,];

    public static function validate($value): void
    {
        if (!in_array($value, self::MAPPING, true)) {
            throw new DomainException('PlanPeriodType name is incorrect');
        }
    }
}
