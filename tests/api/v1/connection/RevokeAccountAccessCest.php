<?php

declare(strict_types=1);

namespace App\Tests\api\v1\connection;

use App\Tests\ApiTester;
use Codeception\Util\HttpCode;

class RevokeAccountAccessCest
{
    private string $url = '/api/v1/connection/revoke-account-access';

    /**
     * @throws \Codeception\Exception\ModuleException
     * @skip
     */
    public function requestShouldReturn200ResponseCode(ApiTester $I): void
    {
        $I->amAuthenticatedAsDany();
        $I->sendPOST($this->url, ['accountId' => '0aaa0450-564e-411e-8018-7003f6dbeb92', 'userId' => 'aff21334-96f0-4fb1-84d8-0223d0280954']);
        $I->seeResponseCodeIs(HttpCode::OK);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     * @skip
     */
    public function requestShouldReturn400ResponseCode(ApiTester $I): void
    {
        $I->amAuthenticatedAsDany();
        $I->sendPOST($this->url, ['unexpected_param' => 'test']);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     * @skip
     */
    public function requestShouldReturn401ResponseCode(ApiTester $I): void
    {
        $I->sendPOST($this->url, ['accountId' => '0aaa0450-564e-411e-8018-7003f6dbeb92', 'userId' => 'aff21334-96f0-4fb1-84d8-0223d0280954']);
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     * @skip
     */
    public function requestShouldReturnResponseWithCorrectStructure(ApiTester $I): void
    {
        $I->amAuthenticatedAsDany();
        $I->sendPOST($this->url, ['accountId' => '0aaa0450-564e-411e-8018-7003f6dbeb92', 'userId' => 'aff21334-96f0-4fb1-84d8-0223d0280954']);
        $I->seeResponseMatchesJsonType([
            'data' => [],
        ]);
    }
}
