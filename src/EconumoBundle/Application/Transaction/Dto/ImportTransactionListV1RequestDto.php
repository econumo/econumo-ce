<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Transaction\Dto;

use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @OA\Schema(
 *     required={"file", "mapping"}
 * )
 */
class ImportTransactionListV1RequestDto
{
    /**
     * @OA\Property(type="string", format="binary", description="CSV file to import")
     */
    public ?UploadedFile $file = null;

    /**
     * @OA\Property(
     *     type="object",
     *     description="Field mapping configuration",
     *     example={"account":"Account Name","date":"Transaction Date","amount":"Amount","amountInflow":null,"amountOutflow":null,"category":"Category","description":"Description","payee":"Merchant","tag":null}
     * )
     */
    public array $mapping = [];

    /**
     * @OA\Property(type="string", format="uuid", description="Override account for all imported rows")
     */
    public ?string $accountId = null;

    /**
     * @OA\Property(type="string", format="date", description="Override date for all imported rows (YYYY-MM-DD)")
     */
    public ?string $date = null;

    /**
     * @OA\Property(type="string", format="uuid", description="Override category for all imported rows")
     */
    public ?string $categoryId = null;

    /**
     * @OA\Property(type="string", description="Override description for all imported rows")
     */
    public ?string $description = null;

    /**
     * @OA\Property(type="string", format="uuid", description="Override payee for all imported rows")
     */
    public ?string $payeeId = null;

    /**
     * @OA\Property(type="string", format="uuid", description="Override tag for all imported rows")
     */
    public ?string $tagId = null;
}
