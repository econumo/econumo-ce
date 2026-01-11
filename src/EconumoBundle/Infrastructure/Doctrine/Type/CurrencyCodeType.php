<?php
declare(strict_types=1);

namespace App\EconumoBundle\Infrastructure\Doctrine\Type;

use App\EconumoBundle\Domain\Entity\ValueObject\CurrencyCode;
use App\EconumoBundle\Infrastructure\Doctrine\Type\ReflectionValueObjectTrait;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class CurrencyCodeType extends StringType
{
    use ReflectionValueObjectTrait;

    /**
     * @inheritdoc
     * @return CurrencyCode|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value === null ? null : $this->getInstance(CurrencyCode::class, $value);
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return 'currency_code_type';
    }
}
