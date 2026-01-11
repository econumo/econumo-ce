<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Entity;

use App\EconumoBundle\Domain\Entity\Account;
use App\EconumoBundle\Domain\Entity\User;
use App\EconumoBundle\Domain\Entity\ValueObject\FolderName;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Events\AccountFolderCreatedEvent;
use App\EconumoBundle\Domain\Traits\EntityTrait;
use App\EconumoBundle\Domain\Traits\EventTrait;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Folder
{
    use EntityTrait;
    use EventTrait;

    private int $position = 1000;

    private bool $isVisible = true;

    /**
     * @var Collection|Account[]
     */
    private Collection $accounts;

    private DateTimeImmutable $createdAt;

    private DateTimeInterface $updatedAt;

    public function __construct(
        private Id $id,
        private User $user,
        private FolderName $name,
        DateTimeInterface $createdAt
    ) {
        $this->accounts = new ArrayCollection();
        $this->createdAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $createdAt->format('Y-m-d H:i:s'));
        $this->updatedAt = DateTime::createFromFormat('Y-m-d H:i:s', $createdAt->format('Y-m-d H:i:s'));
        $this->registerEvent(new AccountFolderCreatedEvent($user->getId(), $id));
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getUserId(): Id
    {
        return $this->user->getId();
    }

    public function getName(): FolderName
    {
        return $this->name;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function isVisible(): bool
    {
        return $this->isVisible;
    }

    public function containsAccount(Account $account): bool
    {
        return $this->accounts->contains($account);
    }

    public function addAccount(Account $account): void
    {
        $this->accounts->add($account);
    }

    public function removeAccount(Account $account): void
    {
        $this->accounts->removeElement($account);
    }

    /**
     * @return Account[]|Collection
     */
    public function getAccounts(): Collection
    {
        return $this->accounts;
    }

    public function updateName(FolderName $name): void
    {
        if (!$this->name->isEqual($name)) {
            $this->name = $name;
            $this->updated();
        }
    }

    public function updatePosition(int $position): void
    {
        if ($this->position !== $position) {
            $this->position = $position;
            $this->updated();
        }
    }

    public function makeVisible(): void
    {
        if (!$this->isVisible) {
            $this->isVisible = true;
            $this->updated();
        }
    }

    public function makeInvisible(): void
    {
        if ($this->isVisible) {
            $this->isVisible = false;
            $this->updated();
        }
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    private function updated(): void
    {
        $this->updatedAt = new DateTime();
    }
}
