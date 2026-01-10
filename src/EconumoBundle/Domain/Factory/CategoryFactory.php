<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Factory;

use App\EconumoBundle\Domain\Entity\Category;
use App\EconumoBundle\Domain\Entity\ValueObject\CategoryName;
use App\EconumoBundle\Domain\Entity\ValueObject\CategoryType;
use App\EconumoBundle\Domain\Entity\ValueObject\Icon;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Repository\CategoryRepositoryInterface;
use App\EconumoBundle\Domain\Factory\CategoryFactoryInterface;
use App\EconumoBundle\Domain\Repository\UserRepositoryInterface;
use App\EconumoBundle\Domain\Service\DatetimeServiceInterface;

class CategoryFactory implements CategoryFactoryInterface
{
    public function __construct(private readonly DatetimeServiceInterface $datetimeService, private readonly UserRepositoryInterface $userRepository, private readonly CategoryRepositoryInterface $categoryRepository)
    {
    }

    public function create(Id $userId, CategoryName $name, CategoryType $type, Icon $icon): Category
    {
        return new Category(
            $this->categoryRepository->getNextIdentity(),
            $this->userRepository->getReference($userId),
            $name,
            $type,
            $icon,
            $this->datetimeService->getCurrentDatetime()
        );
    }
}
