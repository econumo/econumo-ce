<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Entity;

use App\EconumoBundle\Domain\Entity\ValueObject\CurrencyCode;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use App\EconumoBundle\Domain\Entity\ValueObject\TransactionType;
use App\EconumoBundle\Domain\Service\Dto\TransactionDto;
use App\EconumoBundle\Domain\Traits\EntityTrait;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;

class Transaction
{
    use EntityTrait;

    private DateTimeImmutable $createdAt;

    private DateTimeInterface $updatedAt;

    private DateTimeInterface $spentAt;

    public function __construct(
        private Id $id,
        private User $user,
        private TransactionType $type,
        private Account $account,
        private ?Category $category,
        private DecimalNumber $amount,
        DateTimeInterface $transactionDate,
        DateTimeInterface $createdAt,
        private ?Account $accountRecipient,
        private ?DecimalNumber $amountRecipient,
        private string $description,
        private ?Payee $payee,
        private ?Tag $tag
    ) {
        $this->createdAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $createdAt->format('Y-m-d H:i:s'));
        $this->updatedAt = DateTime::createFromFormat('Y-m-d H:i:s', $createdAt->format('Y-m-d H:i:s'));
        $this->spentAt = DateTime::createFromFormat('Y-m-d H:i:s', $transactionDate->format('Y-m-d H:i:s'));
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getUserId(): Id
    {
        return $this->user->getId();
    }

    public function getType(): TransactionType
    {
        return $this->type;
    }

    public function getAccountId(): Id
    {
        return $this->account->getId();
    }

    public function getAccountCurrency(): CurrencyCode
    {
        return $this->account->getCurrencyCode();
    }

    public function getAccountCurrencyId(): Id
    {
        return $this->account->getCurrencyId();
    }

    public function getAccountRecipientId(): ?Id
    {
        return $this->accountRecipient instanceof Account ? $this->accountRecipient->getId() : null;
    }

    public function getAmount(): DecimalNumber
    {
        return $this->amount;
    }

    public function getAmountRecipient(): ?DecimalNumber
    {
        return $this->amountRecipient;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function getCategoryId(): ?Id
    {
        return $this->category instanceof Category ? $this->category->getId() : null;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPayee(): ?Payee
    {
        return $this->payee;
    }

    public function getPayeeId(): ?Id
    {
        return $this->payee instanceof Payee ? $this->payee->getId() : null;
    }

    public function getTag(): ?Tag
    {
        return $this->tag;
    }

    public function getTagId(): ?Id
    {
        return $this->tag instanceof Tag ? $this->tag->getId() : null;
    }

    public function getSpentAt(): DateTimeInterface
    {
        return $this->spentAt;
    }

    public function updateType(TransactionType $transactionType): void
    {
        if (!$this->type->isEqual($transactionType)) {
            $this->type = $transactionType;
            $this->updated();
        }
    }

    public function updateAccount(Account $account): void
    {
        if (!$this->account->getId()->isEqual($account->getId())) {
            $this->account = $account;
            $this->updated();
        }
    }

    public function updateAccountRecipient(?Account $accountRecipient): void
    {
        if ($this->accountRecipient && $accountRecipient && !$this->accountRecipient->getId()->isEqual(
                $accountRecipient->getId()
            )) {
            $this->accountRecipient = $accountRecipient;
            $this->updated();
        } elseif (!$this->accountRecipient && $accountRecipient) {
            $this->accountRecipient = $accountRecipient;
            $this->updated();
        } elseif ($this->accountRecipient && !$accountRecipient) {
            $this->accountRecipient = null;
            $this->updated();
        }
    }

    public function updateAmount(DecimalNumber $amount): void
    {
        if (!$this->amount->isEqual($amount)) {
            $this->amount = $amount;
            $this->updated();
        }
    }

    public function updateAmountRecipient(?DecimalNumber $amount): void
    {
        if (!$this->accountRecipient instanceof Account && $amount instanceof DecimalNumber) {
            $this->amountRecipient = $amount;
            $this->updated();
        } elseif ($this->accountRecipient instanceof Account && !$amount instanceof DecimalNumber) {
            $this->amountRecipient = null;
            $this->updated();
        } elseif ($this->accountRecipient instanceof Account && $amount instanceof DecimalNumber && (!$this->amountRecipient || !$this->amountRecipient->isEqual($amount))) {
            $this->amountRecipient = $amount;
            $this->updated();
        }
    }

    public function updateCategory(?Category $category): void
    {
        if ($this->type->isTransfer()) {
            return;
        }

        if ($this->category && $category && !$this->category->getId()->isEqual($category->getId())) {
            $this->category = $category;
            $this->updated();
        } elseif (!$this->category && $category) {
            $this->category = $category;
            $this->updated();
        } elseif ($this->category && !$category) {
            $this->category = null;
            $this->updated();
        }
    }

    public function updateDescription(?string $description): void
    {
        $newDescription = (string)$description;
        if ($this->description !== $newDescription) {
            $this->description = $newDescription;
            $this->updated();
        }
    }

    public function updatePayee(?Payee $payee): void
    {
        if ($this->type->isTransfer()) {
            return;
        }

        if ($this->payee && $payee && !$this->payee->getId()->isEqual($payee->getId())) {
            $this->payee = $payee;
            $this->updated();
        } elseif (!$this->payee && $payee) {
            $this->payee = $payee;
            $this->updated();
        } elseif ($this->payee && !$payee) {
            $this->payee = null;
            $this->updated();
        }
    }

    public function updateTag(?Tag $tag): void
    {
        if (!$this->type->isExpense()) {
            return;
        }

        if ($this->tag && $tag && !$this->tag->getId()->isEqual($tag->getId())) {
            $this->tag = $tag;
            $this->updated();
        } elseif (!$this->tag && $tag) {
            $this->tag = $tag;
            $this->updated();
        } elseif ($this->tag && !$tag) {
            $this->tag = null;
            $this->updated();
        }
    }

    public function updateDate(DateTimeInterface $dateTime): void
    {
        if ($this->spentAt->format('Y-m-d H:i:s') !== $dateTime->format('Y-m-d H:i:s')) {
            $this->spentAt = DateTime::createFromFormat('Y-m-d H:i:s', $dateTime->format('Y-m-d H:i:s'));
            $this->updated();
        }
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function update(TransactionDto $dto): void
    {
        $this->updateType($dto->type);
        $this->updateAccount($dto->account);
        $this->updateAmount($dto->amount);
        $this->updateDescription($dto->description);
        $this->updateDate($dto->date);
        if ($dto->type->isTransfer()) {
            $this->category = null;
            $this->payee = null;
            $this->tag = null;
            $this->updateAccountRecipient($dto->accountRecipient);
            $this->updateAmountRecipient($dto->amountRecipient);
        } else {
            $this->accountRecipient = null;
            $this->amountRecipient = null;
            $this->updateCategory($dto->category);
            $this->updatePayee($dto->payee);
            $this->updateTag($dto->tag);
        }
    }

    private function updated(): void
    {
        $this->updatedAt = new DateTime();
    }
}
