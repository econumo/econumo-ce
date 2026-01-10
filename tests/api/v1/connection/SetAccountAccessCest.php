<?php

declare(strict_types=1);

namespace App\Tests\api\v1\connection;

use App\Tests\ApiTester;
use Codeception\Util\HttpCode;

class SetAccountAccessCest
{
    private string $url = '/api/v1/connection/set-account-access';

    /**
     * @throws \Codeception\Exception\ModuleException
     * @skip
     */
    public function requestShouldReturn200ResponseCode(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, ['accountId' => '4eec1ee6-1992-4222-b9ab-31ece5eaad5d', 'userId' => '77be9577-147b-4f05-9aa7-91d9b159de5b', 'role' => 'guest']);
        $I->seeResponseCodeIs(HttpCode::OK);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     * @skip
     */
    public function requestShouldReturn400ResponseCode(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, ['unexpected_param' => 'test']);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     * @skip
     */
    public function requestShouldReturn401ResponseCode(ApiTester $I): void
    {
        $I->sendPOST($this->url, ['accountId' => '4eec1ee6-1992-4222-b9ab-31ece5eaad5d', 'userId' => '77be9577-147b-4f05-9aa7-91d9b159de5b', 'role' => 'guest']);
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     * @skip
     */
    public function requestShouldReturnResponseWithCorrectStructure(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, ['accountId' => '4eec1ee6-1992-4222-b9ab-31ece5eaad5d', 'userId' => '77be9577-147b-4f05-9aa7-91d9b159de5b', 'role' => 'guest']);
        $I->seeResponseMatchesJsonType([
            'data' => [],
        ]);
    }
}
