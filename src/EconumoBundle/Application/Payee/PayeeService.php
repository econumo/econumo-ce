<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Payee;

use App\EconumoBundle\Application\Exception\AccessDeniedException;
use App\EconumoBundle\Application\Exception\ValidationException;
use App\EconumoBundle\Application\Payee\Assembler\ArchivePayeeV1ResultAssembler;
use App\EconumoBundle\Application\Payee\Assembler\CreatePayeeV1ResultAssembler;
use App\EconumoBundle\Application\Payee\Assembler\DeletePayeeV1ResultAssembler;
use App\EconumoBundle\Application\Payee\Assembler\UpdatePayeeV1ResultAssembler;
use App\EconumoBundle\Application\Payee\Dto\ArchivePayeeV1RequestDto;
use App\EconumoBundle\Application\Payee\Dto\ArchivePayeeV1ResultDto;
use App\EconumoBundle\Application\Payee\Dto\CreatePayeeV1RequestDto;
use App\EconumoBundle\Application\Payee\Dto\CreatePayeeV1ResultDto;
use App\EconumoBundle\Application\Payee\Dto\DeletePayeeV1RequestDto;
use App\EconumoBundle\Application\Payee\Dto\DeletePayeeV1ResultDto;
use App\EconumoBundle\Application\Payee\Dto\UnarchivePayeeV1RequestDto;
use App\EconumoBundle\Application\Payee\Dto\UnarchivePayeeV1ResultDto;
use App\EconumoBundle\Application\Payee\Assembler\UnarchivePayeeV1ResultAssembler;
use App\EconumoBundle\Application\Payee\Dto\UpdatePayeeV1RequestDto;
use App\EconumoBundle\Application\Payee\Dto\UpdatePayeeV1ResultDto;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\PayeeName;
use App\EconumoBundle\Domain\Exception\PayeeAlreadyExistsException;
use App\EconumoBundle\Domain\Repository\PayeeRepositoryInterface;
use App\EconumoBundle\Domain\Service\AccountAccessServiceInterface;
use App\EconumoBundle\Domain\Service\PayeeServiceInterface;
use App\EconumoBundle\Domain\Service\Translation\TranslationServiceInterface;

class PayeeService
{
    public function __construct(private readonly CreatePayeeV1ResultAssembler $createPayeeV1ResultAssembler, private readonly PayeeServiceInterface $payeeService, private readonly AccountAccessServiceInterface $accountAccessService, private readonly UpdatePayeeV1ResultAssembler $updatePayeeV1ResultAssembler, private readonly PayeeRepositoryInterface $payeeRepository, private readonly DeletePayeeV1ResultAssembler $deletePayeeV1ResultAssembler, private readonly ArchivePayeeV1ResultAssembler $archivePayeeV1ResultAssembler, private readonly UnarchivePayeeV1ResultAssembler $unarchivePayeeV1ResultAssembler, private readonly TranslationServiceInterface $translationService)
    {
    }

    public function createPayee(
        CreatePayeeV1RequestDto $dto,
        Id $userId
    ): CreatePayeeV1ResultDto {
        if ($dto->accountId !== null) {
            $accountId = new Id($dto->accountId);
            $this->accountAccessService->checkAddPayee($userId, $accountId);
            $payee = $this->payeeService->createPayeeForAccount($userId, $accountId, new PayeeName($dto->name));
        } else {
            $payee = $this->payeeService->createPayee($userId, new PayeeName($dto->name));
        }

        return $this->createPayeeV1ResultAssembler->assemble($dto, $payee);
    }

    public function updatePayee(
        UpdatePayeeV1RequestDto $dto,
        Id $userId
    ): UpdatePayeeV1ResultDto {
        $payeeId = new Id($dto->id);
        $tag = $this->payeeRepository->get($payeeId);
        if (!$tag->getUserId()->isEqual($userId)) {
            throw new AccessDeniedException();
        }

        try {
            $this->payeeService->updatePayee($payeeId, new PayeeName($dto->name));
        } catch (PayeeAlreadyExistsException) {
            throw new ValidationException($this->translationService->trans('payee.payee.already_exists', ['name' => $dto->name]));
        }

        return $this->updatePayeeV1ResultAssembler->assemble($dto);
    }

    public function deletePayee(
        DeletePayeeV1RequestDto $dto,
        Id $userId
    ): DeletePayeeV1ResultDto {
        $payeeId = new Id($dto->id);
        $payee = $this->payeeRepository->get($payeeId);
        if (!$payee->getUserId()->isEqual($userId)) {
            throw new AccessDeniedException();
        }

        $this->payeeService->deletePayee($payeeId);
        return $this->deletePayeeV1ResultAssembler->assemble($dto);
    }

    public function archivePayee(
        ArchivePayeeV1RequestDto $dto,
        Id $userId
    ): ArchivePayeeV1ResultDto {
        $payeeId = new Id($dto->id);
        $payee = $this->payeeRepository->get($payeeId);
        if (!$payee->getUserId()->isEqual($userId)) {
            throw new AccessDeniedException();
        }

        $this->payeeService->archivePayee($payeeId);
        return $this->archivePayeeV1ResultAssembler->assemble($dto);
    }

    public function unarchivePayee(
        UnarchivePayeeV1RequestDto $dto,
        Id $userId
    ): UnarchivePayeeV1ResultDto {
        $payeeId = new Id($dto->id);
        $payee = $this->payeeRepository->get($payeeId);
        if (!$payee->getUserId()->isEqual($userId)) {
            throw new AccessDeniedException();
        }

        $this->payeeService->unarchivePayee($payeeId);
        return $this->unarchivePayeeV1ResultAssembler->assemble($dto);
    }
}
