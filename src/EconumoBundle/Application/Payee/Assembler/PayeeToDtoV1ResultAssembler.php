<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Payee\Assembler;

use App\EconumoBundle\Application\Payee\Dto\PayeeResultDto;
use App\EconumoBundle\Domain\Entity\Payee;

class PayeeToDtoV1ResultAssembler
{
    public function assemble(
        Payee $payee
    ): PayeeResultDto {
        $item = new PayeeResultDto();
        $item->id = $payee->getId()->getValue();
        $item->name = $payee->getName()->getValue();
        $item->position = $payee->getPosition();
        $item->ownerUserId = $payee->getUserId()->getValue();
        $item->isArchived = $payee->isArchived() ? 1 : 0;
        $item->createdAt = $payee->getCreatedAt()->format('Y-m-d H:i:s');
        $item->updatedAt = $payee->getUpdatedAt()->format('Y-m-d H:i:s');
        return $item;
    }
}
