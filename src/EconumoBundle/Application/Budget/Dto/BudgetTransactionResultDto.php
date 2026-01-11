<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Dto;

use App\EconumoBundle\Application\User\Dto\UserResultDto;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"id", "author", "currencyId", "amount", "description", "category", "payee", "tag", "spentAt"}
 * )
 */
class BudgetTransactionResultDto
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
     * Currency id
     * @OA\Property(example="f680553f-6b40-407d-a528-5123913be0aa")
     */
    public string $currencyId;

    /**
     * Amount
     * @OA\Property(example="100.5")
     */
    public string $amount;

    /**
     * Description
     * @OA\Property(example="bananas")
     */
    public string $description;

    /**
     * Category
     * @OA\Property()
     */
    public ?BudgetTransactionCategoryResultDto $category = null;

    /**
     * Payee
     * @OA\Property()
     */
    public ?BudgetTransactionPayeeResultDto $payee = null;

    /**
     * Tag id
     * @OA\Property()
     */
    public ?BudgetTransactionTagResultDto $tag = null;

    /**
     * Transaction date
     * @OA\Property(example="2021-08-01 10:00:00")
     */
    public string $spentAt;
}
