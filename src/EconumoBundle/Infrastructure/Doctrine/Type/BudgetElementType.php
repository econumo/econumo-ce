<?php
declare(strict_types=1);

namespace App\EconumoBundle\Infrastructure\Doctrine\Type;

use App\EconumoBundle\Domain\Entity\ValueObject\BudgetElementType as ValueObject;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\SmallIntType;

class BudgetElementType extends SmallIntType
{
    /**
     * @inheritdoc
     * @return ValueObject|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value === null ? null : new ValueObject((int)$value);
    }

    /**
     * @inheritdoc
     * @param int|null $value
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof ValueObject) {
            return $value->getValue();
        }

        return $value;
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return 'budget_element_type';
    }
}
