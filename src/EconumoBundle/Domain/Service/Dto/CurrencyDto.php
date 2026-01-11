<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service\Dto;

use App\EconumoBundle\Domain\Entity\ValueObject\CurrencyCode;

class CurrencyDto
{
    public CurrencyCode $code;

    public ?string $symbol = null;

    public ?string $name = null;

    public ?int $fractionDigits = null;
}
