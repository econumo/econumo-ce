<?php

declare(strict_types=1);

namespace App\Tests\api\v1\transaction;

use App\Tests\ApiTester;
use Codeception\Util\HttpCode;

class CreateTransactionCest
{
    private string $url = '/api/v1/transaction/create-transaction';

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function requestShouldReturn200ResponseCode(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, [
            'id' => '3cc0335e-52ea-11ec-bf63-0242ac130002',
            'type' => 'expense',
            'amount' => 433,
            'amountRecipient' => '',
            'accountId' => '4eec1ee6-1992-4222-b9ab-31ece5eaad5d',
            'accountRecipientId' => '',
            'categoryId' => '95587d1d-2c39-4efc-98f3-23c755da44a4',
            'date' => '2021-12-01 10:00:00',
            'description' => 'Coffee',
            'payeeId' => '701ee173-7c7e-4f92-8af7-a27839c663e0',
            'tagId' => '4b53d029-c1ed-46ad-8d86-1049542f4a7e',
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function requestShouldReturn400ResponseCode(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, ['test' => 'f20ef9c2-52e8-11ec-bf63-0242ac130002']);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function requestShouldReturn401ResponseCode(ApiTester $I): void
    {
        $I->sendPOST($this->url, [
            'id' => '3cc0335e-52ea-11ec-bf63-0242ac130002',
            'type' => 'expense',
            'amount' => 433,
            'amountRecipient' => '',
            'accountId' => '4eec1ee6-1992-4222-b9ab-31ece5eaad5d',
            'accountRecipientId' => '',
            'categoryId' => '95587d1d-2c39-4efc-98f3-23c755da44a4',
            'date' => '2021-12-01 10:00:00',
            'description' => 'Coffee',
            'payeeId' => '701ee173-7c7e-4f92-8af7-a27839c663e0',
            'tagId' => '4b53d029-c1ed-46ad-8d86-1049542f4a7e',
        ]);
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function requestShouldReturnResponseWithCorrectStructure(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, [
            'id' => '3cc0335e-52ea-11ec-bf63-0242ac130002',
            'type' => 'expense',
            'amount' => 433,
            'amountRecipient' => '',
            'accountId' => '4eec1ee6-1992-4222-b9ab-31ece5eaad5d',
            'accountRecipientId' => '',
            'categoryId' => '95587d1d-2c39-4efc-98f3-23c755da44a4',
            'date' => '2021-12-01 10:00:00',
            'description' => 'Coffee',
            'payeeId' => '701ee173-7c7e-4f92-8af7-a27839c663e0',
            'tagId' => '4b53d029-c1ed-46ad-8d86-1049542f4a7e',
        ]);
        $I->seeResponseMatchesJsonType([
            'data' => [
                'accounts' => 'array',
                'item' => $I->getTransactionDtoJsonType(),
            ],
        ]);
        $I->seeResponseMatchesJsonType($I->getAccountDtoJsonType(), '$.data.accounts[0]');
    }
}
