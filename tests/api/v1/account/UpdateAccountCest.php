<?php

declare(strict_types=1);

namespace App\Tests\api\v1\account;

use App\Tests\ApiTester;
use Codeception\Util\HttpCode;

class UpdateAccountCest
{
    private string $url = '/api/v1/account/update-account';

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
        $I->sendPOST($this->url, ['id' => '5f3834d1-34e8-4f60-a697-004e63854513', 'name' => 'Cash RUB', 'balance' => 1000, 'icon' => 'home', 'updatedAt' => '2021-08-19 10:00:00']);
        $I->seeResponseCodeIs(HttpCode::OK);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function requestShouldReturn400ResponseCode(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, ['id' => '', 'name' => '', 'balance' => 1000, 'updatedAt' => '2021-08-19 10:00:00']);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function requestShouldReturnResponseWithCorrectStructure(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, ['id' => '5f3834d1-34e8-4f60-a697-004e63854513', 'name' => 'Cash RUB', 'balance' => 1000, 'icon' => 'home', 'updatedAt' => '2021-08-19 10:00:00']);
        $I->seeResponseMatchesJsonType([
            'data' => [
                'item' => $I->getAccountDtoJsonType(),
                'transaction' => $I->getTransactionDtoJsonType(),
            ],
        ]);
    }
}
