<?php

declare(strict_types=1);

namespace App\Tests\api\v1\user;

use App\Tests\ApiTester;
use Codeception\Util\HttpCode;

class RemindPasswordCest
{
    private string $url = '/api/v1/user/remind-password';

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function requestShouldReturn200ResponseCodeForExistingUser(ApiTester $I): void
    {
        $I->sendPOST($this->url, ['username' => 'john@econumo.test']);
        $I->seeResponseCodeIs(HttpCode::OK);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function requestShouldReturn200ResponseCodeForNonExistingUser(ApiTester $I): void
    {
        $I->sendPOST($this->url, ['username' => 'user@notfound.test']);
        $I->seeResponseCodeIs(HttpCode::OK);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function requestShouldReturn400ResponseCode(ApiTester $I): void
    {
        $I->sendPOST($this->url, ['username' => 'test']);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function requestShouldReturnResponseWithCorrectStructure(ApiTester $I): void
    {
        $I->sendPOST($this->url, ['username' => 'john@econumo.test']);
        $I->seeResponseMatchesJsonType(['data' => []]);
    }
}
