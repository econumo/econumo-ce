<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Factory;


use App\EconumoBundle\Domain\Entity\Currency;
use App\EconumoBundle\Domain\Entity\ValueObject\CurrencyCode;
use App\EconumoBundle\Domain\Service\Dto\CurrencyDto;

interface CurrencyFactoryInterface
{
    public function createByCode(CurrencyCode $code): Currency;

    public function create(CurrencyDto $dto): Currency;
}
