<?php
declare(strict_types=1);

namespace App\EconumoBundle\Domain\Factory;

use App\EconumoBundle\Domain\Entity\Category;
use App\EconumoBundle\Domain\Entity\ValueObject\CategoryName;
use App\EconumoBundle\Domain\Entity\ValueObject\CategoryType;
use App\EconumoBundle\Domain\Entity\ValueObject\Icon;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;

interface CategoryFactoryInterface
{
    public function create(Id $userId, CategoryName $name, CategoryType $type, Icon $icon): Category;
}
