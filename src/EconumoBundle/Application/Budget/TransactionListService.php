<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget;

use App\EconumoBundle\Application\Budget\Dto\GetTransactionListV1RequestDto;
use App\EconumoBundle\Application\Budget\Dto\GetBudgetTransactionListV1ResultDto;
use App\EconumoBundle\Application\Budget\Assembler\GetTransactionListV1ResultAssembler;
use App\EconumoBundle\Application\Exception\AccessDeniedException;
use App\EconumoBundle\Application\Exception\ValidationException;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Service\Budget\BudgetAccessServiceInterface;
use App\EconumoBundle\Domain\Service\Budget\BudgetTransactionServiceInterface;
use DateTimeImmutable;

readonly class TransactionListService
{
    public function __construct(
        private GetTransactionListV1ResultAssembler $getTransactionListV1ResultAssembler,
        private BudgetTransactionServiceInterface $budgetTransactionService,
        private BudgetAccessServiceInterface $budgetAccessService,
    ) {
    }

    public function getTransactionList(
        GetTransactionListV1RequestDto $dto,
        Id $userId
    ): GetBudgetTransactionListV1ResultDto {
        $budgetId = new Id($dto->budgetId);
        $periodStart = DateTimeImmutable::createFromFormat('Y-m-d', $dto->periodStart);
        $tagId = empty($dto->tagId) ? null : new Id($dto->tagId);
        $categoryId = empty($dto->categoryId) ? null : new Id($dto->categoryId);
        $envelopeId = empty($dto->envelopeId) ? null : new Id($dto->envelopeId);
        if (!$this->budgetAccessService->canReadBudget($userId, $budgetId)) {
            throw new AccessDeniedException();
        }

        if ($categoryId instanceof Id && !$tagId instanceof Id && !$envelopeId instanceof Id) {
            $transactions = $this->budgetTransactionService->getTransactionsForCategory($userId, $budgetId, $periodStart, $categoryId);
        } elseif ($tagId instanceof Id && !$envelopeId instanceof Id) {
            $transactions = $this->budgetTransactionService->getTransactionsForTag($userId, $budgetId, $periodStart, $tagId, $categoryId);
        } elseif ($envelopeId instanceof Id && !$tagId instanceof Id && !$categoryId instanceof Id) {
            $transactions = $this->budgetTransactionService->getTransactionsForEnvelope($userId, $budgetId, $periodStart, $envelopeId);
        } else {
            throw new ValidationException();
        }

        return $this->getTransactionListV1ResultAssembler->assemble($transactions);
    }
}
