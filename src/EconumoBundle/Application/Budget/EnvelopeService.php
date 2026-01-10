<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget;

use App\EconumoBundle\Application\Budget\Dto\CreateEnvelopeV1RequestDto;
use App\EconumoBundle\Application\Budget\Dto\CreateEnvelopeV1ResultDto;
use App\EconumoBundle\Application\Budget\Assembler\CreateEnvelopeV1ResultAssembler;
use App\EconumoBundle\Application\Exception\AccessDeniedException;
use App\EconumoBundle\Domain\Entity\ValueObject\BudgetEnvelopeName;
use App\EconumoBundle\Domain\Entity\ValueObject\Icon;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Service\Budget\BudgetAccessServiceInterface;
use App\EconumoBundle\Domain\Service\Budget\BudgetEnvelopeServiceInterface;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetEnvelopeDto;
use App\EconumoBundle\Application\Budget\Dto\UpdateEnvelopeV1RequestDto;
use App\EconumoBundle\Application\Budget\Dto\UpdateEnvelopeV1ResultDto;
use App\EconumoBundle\Application\Budget\Assembler\UpdateEnvelopeV1ResultAssembler;
use App\EconumoBundle\Application\Budget\Dto\DeleteEnvelopeV1RequestDto;
use App\EconumoBundle\Application\Budget\Dto\DeleteEnvelopeV1ResultDto;
use App\EconumoBundle\Application\Budget\Assembler\DeleteEnvelopeV1ResultAssembler;

readonly class EnvelopeService
{
    public function __construct(
        private CreateEnvelopeV1ResultAssembler $createEnvelopeV1ResultAssembler,
        private BudgetAccessServiceInterface $budgetAccessService,
        private BudgetEnvelopeServiceInterface $budgetEnvelopeService,
        private UpdateEnvelopeV1ResultAssembler $updateEnvelopeV1ResultAssembler,
        private DeleteEnvelopeV1ResultAssembler $deleteEnvelopeV1ResultAssembler,
    ) {
    }

    public function createEnvelope(
        CreateEnvelopeV1RequestDto $dto,
        Id $userId
    ): CreateEnvelopeV1ResultDto {
        $budgetId = new Id($dto->budgetId);
        if (!$this->budgetAccessService->canUpdateBudget($userId, $budgetId)) {
            throw new AccessDeniedException();
        }

        $envelopeDto = new BudgetEnvelopeDto(
            new Id($dto->id),
            new Id($dto->currencyId),
            new BudgetEnvelopeName($dto->name),
            new Icon($dto->icon),
            0,
            false,
            array_map(static fn(string $id): Id => new Id($id), $dto->categories)
        );
        $folderId = $dto->folderId === null ? null : new Id($dto->folderId);
        $element = $this->budgetEnvelopeService->create($budgetId, $envelopeDto, $folderId);
        return $this->createEnvelopeV1ResultAssembler->assemble($element);
    }

    public function updateEnvelope(
        UpdateEnvelopeV1RequestDto $dto,
        Id $userId
    ): UpdateEnvelopeV1ResultDto {
        $budgetId = new Id($dto->budgetId);
        if (!$this->budgetAccessService->canUpdateBudget($userId, $budgetId)) {
            throw new AccessDeniedException();
        }

        $envelopeDto = new BudgetEnvelopeDto(
            new Id($dto->id),
            new Id($dto->currencyId),
            new BudgetEnvelopeName($dto->name),
            new Icon($dto->icon),
            0,
            (bool)$dto->isArchived,
            array_map(static fn(string $id): Id => new Id($id), $dto->categories)
        );
        $element = $this->budgetEnvelopeService->update($budgetId, $envelopeDto);
        return $this->updateEnvelopeV1ResultAssembler->assemble($element);
    }

    public function deleteEnvelope(
        DeleteEnvelopeV1RequestDto $dto,
        Id $userId
    ): DeleteEnvelopeV1ResultDto {
        $budgetId = new Id($dto->budgetId);
        if (!$this->budgetAccessService->canDeleteBudget($userId, $budgetId)) {
            throw new AccessDeniedException();
        }

        $envelopeId = new Id($dto->id);

        $this->budgetEnvelopeService->delete($budgetId, $envelopeId);
        return $this->deleteEnvelopeV1ResultAssembler->assemble();
    }
}
