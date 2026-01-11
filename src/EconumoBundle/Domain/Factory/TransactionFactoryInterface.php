<?php
declare(strict_types=1);

namespace App\EconumoBundle\Domain\Factory;

use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use DateTimeInterface;
use App\EconumoBundle\Domain\Entity\Transaction;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Service\Dto\TransactionDto;

interface TransactionFactoryInterface
{
    public function create(TransactionDto $dto): Transaction;

    public function createTransaction(Id $accountId, DecimalNumber $transaction, DateTimeInterface $transactionDate, string $comment = ''): Transaction;

    public function createCorrection(Id $accountId, DecimalNumber $correction, DateTimeInterface $transactionDate, string $comment = ''): Transaction;
}
