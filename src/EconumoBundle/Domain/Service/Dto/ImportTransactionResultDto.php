<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service\Dto;

class ImportTransactionResultDto
{
    public int $imported = 0;

    public int $skipped = 0;

    /**
     * @var array<string, int[]>
     */
    public array $errors = [];
}
