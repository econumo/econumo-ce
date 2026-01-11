<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Currency;

use App\EconumoBundle\Application\Currency\Dto\GetCurrencyListV1RequestDto;
use App\EconumoBundle\Application\Currency\Dto\GetCurrencyListV1ResultDto;
use App\EconumoBundle\Application\Currency\Assembler\GetCurrencyListV1ResultAssembler;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Repository\CurrencyRepositoryInterface;

class CurrencyListService
{
    public function __construct(private readonly GetCurrencyListV1ResultAssembler $getCurrencyListV1ResultAssembler, private readonly CurrencyRepositoryInterface $currencyRepository)
    {
    }

    public function getCurrencyList(
        GetCurrencyListV1RequestDto $dto,
        Id $userId
    ): GetCurrencyListV1ResultDto {
        $currencies = $this->currencyRepository->getAll();
        return $this->getCurrencyListV1ResultAssembler->assemble($dto, $currencies);
    }
}
