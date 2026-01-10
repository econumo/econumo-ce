<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Factory;


use App\EconumoBundle\Domain\Entity\Tag;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\TagName;
use App\EconumoBundle\Domain\Repository\TagRepositoryInterface;
use App\EconumoBundle\Domain\Factory\TagFactoryInterface;
use App\EconumoBundle\Domain\Repository\UserRepositoryInterface;
use App\EconumoBundle\Domain\Service\DatetimeServiceInterface;

class TagFactory implements TagFactoryInterface
{
    public function __construct(private readonly DatetimeServiceInterface $datetimeService, private readonly TagRepositoryInterface $tagRepository, private readonly UserRepositoryInterface $userRepository)
    {
    }

    public function create(Id $userId, TagName $name): Tag
    {
        return new Tag(
            $this->tagRepository->getNextIdentity(),
            $this->userRepository->getReference($userId),
            $name,
            $this->datetimeService->getCurrentDatetime()
        );
    }
}
