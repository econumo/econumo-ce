<?php

declare(strict_types=1);

namespace App\Tests\api\v1\account;

use App\Tests\ApiTester;
use Codeception\Util\HttpCode;

class CreateAccountCest
{
    private string $url = '/api/v1/account/create-account';

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function requestShouldReturn200ResponseCode(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, [
            'id' => '4b7946ca-2a48-4ea3-8645-2960cea6b94f',
            'name' => 'Savings Account',
            'currencyId' => 'fe5d9269-b69c-4841-9c04-136225447eca',
            'balance' => 100.13,
            'icon' => 'savings',
            'folderId' => '1ad16d32-36af-496e-9867-3919436b8d86'
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function requestShouldReturn400ResponseCode(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, [
            'id' => '',
            'name' => '',
            'currencyId' => '',
            'balance' => 0,
            'icon' => 'no',
            'folderId' => '1ad16d32-36af-496e-9867-3919436b8d86'
        ]);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function requestShouldReturn401ResponseCode(ApiTester $I): void
    {
        $I->sendPOST($this->url, ['id' => 'test']);
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function requestShouldReturnResponseWithCorrectStructure(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, [
            'id' => '4b7946ca-2a48-4ea3-8645-2960cea6b94e',
            'name' => 'Savings Account',
            'currencyId' => 'fe5d9269-b69c-4841-9c04-136225447eca',
            'balance' => 100.13,
            'icon' => 'savings',
            'folderId' => '1ad16d32-36af-496e-9867-3919436b8d86'
        ]);
        $I->seeResponseMatchesJsonType([
            'data' => [
                'item' => $I->getAccountDtoJsonType(),
            ],
        ]);
        $data = $I->grabDataFromResponseByJsonPath('$.data.item.sharedAccess[0]');
        $I->assertEmpty($data);
    }
}
