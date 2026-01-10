<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service;


use App\EconumoBundle\Domain\Entity\Tag;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\TagName;
use App\EconumoBundle\Domain\Exception\TagAlreadyExistsException;
use App\EconumoBundle\Domain\Factory\TagFactoryInterface;
use App\EconumoBundle\Domain\Repository\AccountRepositoryInterface;
use App\EconumoBundle\Domain\Repository\TagRepositoryInterface;
use App\EconumoBundle\Domain\Service\AntiCorruptionServiceInterface;
use App\EconumoBundle\Domain\Service\Budget\BudgetElementServiceInterface;
use App\EconumoBundle\Domain\Service\TagServiceInterface;
use DateTimeInterface;

readonly class TagService implements TagServiceInterface
{
    public function __construct(
        private TagFactoryInterface $tagFactory,
        private TagRepositoryInterface $tagRepository,
        private AccountRepositoryInterface $accountRepository,
        private AntiCorruptionServiceInterface $antiCorruptionService,
        private BudgetElementServiceInterface $budgetElementService,
    ) {
    }

    public function createTag(Id $userId, TagName $name): Tag
    {
        $tags = $this->tagRepository->findByOwnerId($userId);
        foreach ($tags as $tag) {
            if ($tag->getName()->isEqual($name)) {
                throw new TagAlreadyExistsException();
            }
        }

        $this->antiCorruptionService->beginTransaction(__METHOD__);
        try {
            $tag = $this->tagFactory->create($userId, $name);
            $tag->updatePosition(count($tags));
            $this->tagRepository->save([$tag]);
            $this->budgetElementService->createTagElements($tag);

            $this->antiCorruptionService->commit(__METHOD__);
        } catch (\Throwable $throwable) {
            $this->antiCorruptionService->rollback(__METHOD__);
            throw $throwable;
        }

        return $tag;
    }

    public function createTagForAccount(Id $userId, Id $accountId, TagName $name): Tag
    {
        $account = $this->accountRepository->get($accountId);
        if ($userId->isEqual($account->getUserId())) {
            return $this->createTag($userId, $name);
        }

        return $this->createTag($account->getUserId(), $name);
    }

    public function updateTag(Id $tagId, TagName $name): void
    {
        $tag = $this->tagRepository->get($tagId);
        $userTags = $this->tagRepository->findByOwnerId($tag->getUserId());
        foreach ($userTags as $userTag) {
            if ($userTag->getName()->isEqual($name) && !$userTag->getId()->isEqual($tagId)) {
                throw new TagAlreadyExistsException();
            }
        }

        $tag->updateName($name);
        $this->tagRepository->save([$tag]);
    }

    public function orderTags(Id $userId, array $changes): void
    {
        $tags = $this->tagRepository->findAvailableForUserId($userId);
        $changed = [];
        foreach ($tags as $tag) {
            foreach ($changes as $change) {
                if ($tag->getId()->isEqual($change->getId())) {
                    $tag->updatePosition($change->position);
                    $changed[] = $tag;
                    break;
                }
            }
        }

        if ($changed === []) {
            return;
        }

        $this->tagRepository->save($changed);
    }

    public function deleteTag(Id $tagId): void
    {
        $this->antiCorruptionService->beginTransaction(__METHOD__);
        try {
            $category = $this->tagRepository->get($tagId);
            $this->budgetElementService->deleteTagElements($tagId);
            $this->tagRepository->delete($category);
            $this->antiCorruptionService->commit(__METHOD__);
        } catch (\Throwable $throwable) {
            $this->antiCorruptionService->rollback(__METHOD__);
            throw $throwable;
        }
    }

    public function archiveTag(Id $tagId): void
    {
        $this->antiCorruptionService->beginTransaction(__METHOD__);
        try {
            $tag = $this->tagRepository->get($tagId);
            $tag->archive();
            $this->budgetElementService->archiveTagElements($tagId);
            $this->tagRepository->save([$tag]);

            $this->antiCorruptionService->commit(__METHOD__);
        } catch (Throwable $throwable) {
            $this->antiCorruptionService->rollback(__METHOD__);
            throw $throwable;
        }
    }

    public function unarchiveTag(Id $tagId): void
    {
        $this->antiCorruptionService->beginTransaction(__METHOD__);
        try {
            $tag = $this->tagRepository->get($tagId);
            $tag->unarchive();
            $this->budgetElementService->unarchiveTagElements($tagId);
            $this->tagRepository->save([$tag]);

            $this->antiCorruptionService->commit(__METHOD__);
        } catch (Throwable $throwable) {
            $this->antiCorruptionService->rollback(__METHOD__);
            throw $throwable;
        }
    }

    public function getChanged(Id $userId, DateTimeInterface $lastUpdate): array
    {
        $tags = $this->tagRepository->findAvailableForUserId($userId);
        $result = [];
        foreach ($tags as $tag) {
            if ($tag->getUpdatedAt() > $lastUpdate) {
                $result[] = $tag;
            }
        }

        return $result;
    }
}
