<?php
declare(strict_types=1);

namespace App\EconumoBundle\Application\Transaction\Dto;

use App\EconumoBundle\Application\Category\Dto\CategoryResultDto;
use App\EconumoBundle\Application\Payee\Dto\PayeeResultDto;
use App\EconumoBundle\Application\Tag\Dto\TagResultDto;
use App\EconumoBundle\Application\User\Dto\UserResultDto;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"id", "author", "type", "accountId", "amount", "description", "date"}
 * )
 */
class TransactionResultDto
{
    /**
     * Id
     * @var string
     * @OA\Property(example="1b8559ac-4c77-47e4-a95c-1530a5274ab7")
     */
    public string $id;

    /**
     * Author of transaction
     * @var UserResultDto
     * @OA\Property()
     */
    public UserResultDto $author;

    /**
     * Transaction type
     * @OA\Property(example="expense")
     */
    public string $type;

    /**
     * Account id
     * @OA\Property(example="f680553f-6b40-407d-a528-5123913be0aa")
     */
    public string $accountId;

    /**
     * Account recipient id
     * @OA\Property(example="f680553f-6b40-407d-a528-5123913be0aa")
     */
    public ?string $accountRecipientId = null;

    /**
     * Amount
     * @OA\Property(example="100.5")
     */
    public string $amount;

    /**
     * Amount recipient
     * @OA\Property(example="100.5")
     */
    public ?string $amountRecipient = null;

    /**
     * Category id
     * @OA\Property(example="1b8559ac-4c77-47e4-a95c-1530a5274ab7")
     */
    public ?string $categoryId = null;

    /**
     * Description
     * @OA\Property(example="bananas")
     */
    public string $description;

    /**
     * Payee id
     * @OA\Property(example="1b8559ac-4c77-47e4-a95c-1530a5274ab7")
     */
    public ?string $payeeId = null;

    /**
     * Tag id
     * @OA\Property(example="1b8559ac-4c77-47e4-a95c-1530a5274ab7")
     */
    public ?string $tagId = null;

    /**
     * Transaction date
     * @OA\Property(example="2021-08-01 10:00:00")
     */
    public string $date;
}
