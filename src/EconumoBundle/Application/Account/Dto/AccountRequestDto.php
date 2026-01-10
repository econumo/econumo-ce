<?php
declare(strict_types=1);

namespace App\EconumoBundle\Application\Account\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"id", "name", "position", "currencyId", "balance"}
 * )
 */
class AccountRequestDto
{
    /**
     * Id
     * @var string
     * @OA\Property(example="a5e2eee2-56aa-43c6-a827-ca155683ea8d")
     */
    public string $id;

    /**
     * Account name
     * @var string
     * @OA\Property(example="Cash")
     */
    public string $name;

    /**
     * Position
     * @var int
     * @OA\Property(example="1")
     */
    public int $position;

    /**
     * Currency Id
     * @var string
     * @OA\Property(example="77adad8a-9982-4e08-8fd7-5ef336c7a5c9")
     */
    public string $currencyId;

    /**
     * Current balance
     * @var float|string
     * @OA\Property(example="13.07")
     */
    public float|string $balance;
}
