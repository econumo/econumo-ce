<?php
declare(strict_types=1);

namespace App\EconumoBundle\Application\Account\Dto;

use App\EconumoBundle\Application\Account\Dto\SharedAccessItemResultDto;
use App\EconumoBundle\Application\Currency\Dto\CurrencyResultDto;
use App\EconumoBundle\Application\User\Dto\UserResultDto;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"id", "owner", "name", "position", "currency", "balance", "type", "icon", "sharedAccess"}
 * )
 */
class AccountResultDto
{
    /**
     * Id
     * @var string
     * @OA\Property(example="a5e2eee2-56aa-43c6-a827-ca155683ea8d")
     */
    public string $id;

    /**
     * @OA\Property()
     */
    public UserResultDto $owner;

    /**
     * Account folder id
     * @OA\Property(example="1ad16d32-36af-496e-9867-3919436b8d86")
     */
    public ?string $folderId = null;

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
     * Currency
     * @OA\Property()
     */
    public CurrencyResultDto $currency;

    /**
     * Current balance
     * @OA\Property(example="13.07")
     */
    public string $balance;

    /**
     * Account type
     * @var int
     * @OA\Property(example="1")
     */
    public int $type;

    /**
     * Account icon
     * @var string
     * @OA\Property(example="rounded_corner")
     */
    public string $icon;

    /**
     * Account access
     * @var SharedAccessItemResultDto[]
     * @OA\Property()
     */
    public array $sharedAccess = [];
}
