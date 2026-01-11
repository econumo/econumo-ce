<?php
declare(strict_types=1);

namespace App\EconumoBundle\Infrastructure\Doctrine\Type;

use App\EconumoBundle\Domain\Entity\ValueObject\PayeeName;
use App\EconumoBundle\Infrastructure\Doctrine\Type\ReflectionValueObjectTrait;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class PayeeNameType extends StringType
{
    use ReflectionValueObjectTrait;

    /**
     * @inheritdoc
     * @return PayeeName|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value === null ? null : $this->getInstance(PayeeName::class, $value);
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return 'payee_name_type';
    }
}
