<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Payee\Assembler;

use App\EconumoBundle\Application\Payee\Assembler\PayeeToDtoV1ResultAssembler;
use App\EconumoBundle\Application\Payee\Dto\PayeeResultDto;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Repository\PayeeRepositoryInterface;

class PayeeIdToDtoV1ResultAssembler
{
    public function __construct(private readonly PayeeRepositoryInterface $payeeRepository, private readonly PayeeToDtoV1ResultAssembler $payeeToDtoV1ResultAssembler)
    {
    }

    public function assemble(
        Id $payeeId
    ): PayeeResultDto {
        $payee = $this->payeeRepository->get($payeeId);
        return $this->payeeToDtoV1ResultAssembler->assemble($payee);
    }
}
