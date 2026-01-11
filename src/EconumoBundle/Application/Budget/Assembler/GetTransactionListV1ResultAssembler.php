<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Assembler;

use App\EconumoBundle\Domain\Entity\Category;
use App\EconumoBundle\Domain\Entity\Payee;
use App\EconumoBundle\Domain\Entity\Tag;
use App\EconumoBundle\Application\Budget\Dto\BudgetTransactionCategoryResultDto;
use App\EconumoBundle\Application\Budget\Dto\BudgetTransactionPayeeResultDto;
use App\EconumoBundle\Application\Budget\Dto\BudgetTransactionResultDto;
use App\EconumoBundle\Application\Budget\Dto\BudgetTransactionTagResultDto;
use App\EconumoBundle\Application\Budget\Dto\GetBudgetTransactionListV1ResultDto;
use App\EconumoBundle\Application\User\Assembler\UserToDtoResultAssembler;
use App\EconumoBundle\Domain\Entity\Transaction;

readonly class GetTransactionListV1ResultAssembler
{
    public function __construct(
        private UserToDtoResultAssembler  $userToDtoResultAssembler
    ) {
    }

    /**
     * @param Transaction[] $transactions
     */
    public function assemble(
        array $transactions
    ): GetBudgetTransactionListV1ResultDto {
        $result = new GetBudgetTransactionListV1ResultDto();
        $result->items = [];
        foreach ($transactions as $transaction) {
            $dto = new BudgetTransactionResultDto();
            $dto->id = $transaction->getId()->getValue();
            $dto->author = $this->userToDtoResultAssembler->assemble($transaction->getUser());
            $dto->description = $transaction->getDescription();
            $dto->currencyId = $transaction->getAccountCurrencyId()->getValue();
            $dto->amount = $transaction->getAmount()->getValue();
            $dto->spentAt = $transaction->getSpentAt()->format('Y-m-d H:i:s');
            $dto->category = null;
            if ($transaction->getCategory() instanceof Category) {
                $dto->category = new BudgetTransactionCategoryResultDto();
                $dto->category->id = $transaction->getCategory()->getId()->getValue();
                $dto->category->name = $transaction->getCategory()->getName()->getValue();
                $dto->category->icon = $transaction->getCategory()->getIcon()->getValue();
            }

            $dto->payee = null;
            if ($transaction->getPayee() instanceof Payee) {
                $dto->payee = new BudgetTransactionPayeeResultDto();
                $dto->payee->id = $transaction->getPayee()->getId()->getValue();
                $dto->payee->name = $transaction->getPayee()->getName()->getValue();
            }

            $dto->tag = null;
            if ($transaction->getTag() instanceof Tag) {
                $dto->tag = new BudgetTransactionTagResultDto();
                $dto->tag->id = $transaction->getTag()->getId()->getValue();
                $dto->tag->name = $transaction->getTag()->getName()->getValue();
            }

            $result->items[] = $dto;
        }

        return $result;
    }
}
