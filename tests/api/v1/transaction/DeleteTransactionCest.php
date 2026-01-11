<?php

declare(strict_types=1);

namespace App\Tests\api\v1\transaction;

use App\Tests\ApiTester;
use Codeception\Util\HttpCode;

class DeleteTransactionCest
{
    private string $url = '/api/v1/transaction/delete-transaction';

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function requestShouldReturn401ResponseCode(ApiTester $I): void
    {
        $I->sendPOST($this->url);
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function requestShouldReturn200ResponseCode(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, ['id' => '7cb3227d-22dc-4178-aeb4-02a8f815bdbd']);
        $I->seeResponseCodeIs(HttpCode::OK);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function requestShouldReturn400ResponseCode(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, ['unexpected_param' => 'test']);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function requestShouldReturnResponseWithCorrectStructure(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, ['id' => '7cb3227d-22dc-4178-aeb4-02a8f815bdbd']);
        $I->seeResponseMatchesJsonType([
            'data' => [
                'accounts' => 'array',
                'item' => $I->getTransactionDtoJsonType()
            ],
        ]);
        $I->seeResponseMatchesJsonType($I->getAccountDtoJsonType(), '$.data.accounts[0]');
    }
}
