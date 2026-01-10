<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Currency\Assembler;

use App\EconumoBundle\Application\Currency\Dto\CurrencyResultDto;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Repository\CurrencyRepositoryInterface;

readonly class CurrencyIdToDtoV1ResultAssembler
{
    public function __construct(
        private CurrencyRepositoryInterface $currencyRepository,
        private CurrencyToDtoV1ResultAssembler $currencyToDtoV1ResultAssembler
    ) {
    }

    public function assemble(Id $currencyId): CurrencyResultDto
    {
        $currency = $this->currencyRepository->get($currencyId);
        return $this->currencyToDtoV1ResultAssembler->assemble($currency);
    }
}
