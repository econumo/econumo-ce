<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Transaction\Assembler;

use App\EconumoBundle\Application\Transaction\Dto\ImportTransactionListV1ResultDto;
use App\EconumoBundle\Domain\Service\Dto\ImportTransactionResultDto;

readonly class ImportTransactionListV1ResultAssembler
{
    public function assemble(
        ImportTransactionResultDto $domainResult
    ): ImportTransactionListV1ResultDto {
        $result = new ImportTransactionListV1ResultDto();
        $result->imported = $domainResult->imported;
        $result->skipped = $domainResult->skipped;
        $result->errors = $domainResult->errors;

        return $result;
    }
}
