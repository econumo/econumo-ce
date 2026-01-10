<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service;

use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Service\Dto\ImportTransactionResultDto;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ImportTransactionServiceInterface
{
    /**
     * Import transactions from CSV file
     *
     * @param UploadedFile $file CSV file to import
     * @param array $mapping Field mapping configuration
     * @param Id $userId User ID
     * @return ImportTransactionResultDto Import result with statistics
     */
    public function importFromCsv(
        UploadedFile $file,
        array $mapping,
        Id $userId,
        array $overrides = []
    ): ImportTransactionResultDto;
}
