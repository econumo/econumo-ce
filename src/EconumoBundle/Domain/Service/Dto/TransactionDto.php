<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service\Dto;

use App\EconumoBundle\Domain\Entity\Account;
use App\EconumoBundle\Domain\Entity\Category;
use App\EconumoBundle\Domain\Entity\Payee;
use App\EconumoBundle\Domain\Entity\Tag;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use App\EconumoBundle\Domain\Entity\ValueObject\TransactionType;
use DateTimeInterface;

class TransactionDto
{
    public TransactionType $type;

    public Id $userId;

    public DecimalNumber $amount;

    public ?DecimalNumber $amountRecipient = null;

    public Id $accountId;

    public Account $account;

    public ?Id $accountRecipientId = null;

    public ?Account $accountRecipient = null;

    public ?Id $categoryId = null;

    public ?Category $category = null;

    public DateTimeInterface $date;

    public string $description;

    public ?Id $payeeId = null;

    public ?Payee $payee = null;

    public ?Id $tagId = null;

    public ?Tag $tag = null;
}
