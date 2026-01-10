<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Entity;

use App\EconumoBundle\Domain\Entity\ValueObject\BudgetName;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Traits\EntityTrait;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Budget
{
    use EntityTrait;

    private DateTimeInterface $startedAt;

    private DateTimeImmutable $createdAt;

    private DateTimeInterface $updatedAt;

    /**
     * @var Collection|Account[]
     */
    private Collection $excludedAccounts;

    /**
     * @var Collection|BudgetAccess[]
     */
    private Collection $budgetAccess;

    /**
     * @var Collection|BudgetFolder[]
     */
    private Collection $budgetFolders;

    public function __construct(
        private User $user,
        private Id $id,
        private BudgetName $name,
        private Currency $currency,
        array $excludedAccounts,
        DateTimeInterface $startDate,
        DateTimeInterface $createdAt
    ) {
        $this->startedAt = DateTime::createFromFormat('Y-m-d H:i:s', $startDate->format('Y-m-01 00:00:00'));
        $this->createdAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $createdAt->format('Y-m-d H:i:s'));
        $this->updatedAt = DateTime::createFromFormat('Y-m-d H:i:s', $createdAt->format('Y-m-d H:i:s'));
        $this->excludedAccounts = new ArrayCollection();
        foreach ($excludedAccounts as $excludedAccount) {
            if ($excludedAccount instanceof Account) {
                $this->excludeAccount($excludedAccount);
            }
        }

        $this->budgetAccess = new ArrayCollection();
        $this->budgetFolders = new ArrayCollection();
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getName(): BudgetName
    {
        return $this->name;
    }

    public function updateName(BudgetName $name): void
    {
        if (!$this->name->isEqual($name)) {
            $this->name = $name;
            $this->updated();
        }
    }

    public function startFrom(DateTimeInterface $startedAt): void
    {
        $this->startedAt = DateTime::createFromFormat('Y-m-d H:i:s', $startedAt->format('Y-m-01 00:00:00'));
        $this->updated();
    }

    public function getStartedAt(): DateTimeInterface
    {
        return $this->startedAt;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function updateCurrency(Currency $currency): void
    {
        if (!$this->currency->getId()->isEqual($currency->getId())) {
            $this->currency = $currency;
            $this->updated();
        }
    }

    public function getCurrencyId(): Id
    {
        return $this->currency->getId();
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return Collection|Account[]
     */
    public function getExcludedAccounts(Id $userId = null): Collection
    {
        if ($userId instanceof Id) {
            return $this->excludedAccounts->filter(
                static fn(Account $account): bool => $account->getUserId()->isEqual($userId)
            );
        }

        return $this->excludedAccounts;
    }

    public function excludeAccount(Account $account): self
    {
        if (!$this->excludedAccounts->contains($account)) {
            $this->excludedAccounts->add($account);
            $this->updated();
        }

        return $this;
    }

    public function includeAccount(Account $account): self
    {
        if ($this->excludedAccounts->contains($account)) {
            $this->excludedAccounts->removeElement($account);
            $this->updated();
        }

        return $this;
    }

    /**
     * @return Collection|BudgetAccess[]
     */
    public function getAccessList(): Collection
    {
        return $this->budgetAccess;
    }

    public function isUserAccepted(Id $userId): bool
    {
        if ($this->user->getId()->isEqual($userId)) {
            return true;
        }

        foreach ($this->getAccessList() as $budgetAccess) {
            if ($budgetAccess->getUserId()->isEqual($userId)) {
                return $budgetAccess->isAccepted();
            }
        }

        return false;
    }

    /**
     * @return Collection|BudgetFolder[]
     */
    public function getFolderList(): Collection
    {
        return $this->budgetFolders;
    }

    private function updated(): void
    {
        $this->updatedAt = new DateTime();
    }
}