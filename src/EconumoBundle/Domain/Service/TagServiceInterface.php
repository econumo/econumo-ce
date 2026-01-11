<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service;


use App\EconumoBundle\Domain\Entity\Tag;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\TagName;
use App\EconumoBundle\Domain\Service\Dto\PositionDto;
use DateTimeInterface;

interface TagServiceInterface
{
    public function createTag(Id $userId, TagName $name): Tag;

    public function createTagForAccount(Id $userId, Id $accountId, TagName $name): Tag;

    public function updateTag(Id $tagId, TagName $name): void;

    /**
     * @param Id $userId
     * @param PositionDto[] $changes
     * @return void
     */
    public function orderTags(Id $userId, array $changes): void;

    public function deleteTag(Id $tagId): void;

    public function archiveTag(Id $tagId): void;

    public function unarchiveTag(Id $tagId): void;
}
