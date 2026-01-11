<?php

declare(strict_types=1);

namespace App\Tests\api\v1\user;

use Codeception\Exception\ModuleException;
use App\Tests\ApiTester;
use Codeception\Util\HttpCode;

class ResetPasswordCest
{
    private string $url = '/api/v1/user/reset-password';

    /**
     * @throws ModuleException
     */
    public function requestShouldReturn400ResponseCodeIfWrongCode(ApiTester $I): void
    {
        $I->sendPOST($this->url, ['username' => 'john@econumo.test', 'code' => '111111111111', 'password' => 'pass']);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    /**
     * @throws ModuleException
     */
    public function requestShouldReturn400ResponseCodeIfUserNotExists(ApiTester $I): void
    {
        $I->sendPOST($this->url, ['username' => 'john1@econumo.test', 'code' => '39e83221911d', 'password' => 'pass']);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    /**
     * @throws ModuleException
     */
    public function requestShouldReturn400ResponseCodeIfCodeNotExists(ApiTester $I): void
    {
        $I->sendPOST($this->url, ['username' => 'margo@econumo.test', 'code' => '111111111111', 'password' => 'pass']);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    /**
     * @throws ModuleException
     */
    public function requestShouldReturn400ResponseCodeIfExpired(ApiTester $I): void
    {
        $I->sendPOST($this->url, ['username' => 'dany@econumo.test', 'code' => 'f9dedac8b623', 'password' => 'pass']);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    /**
     * @throws ModuleException
     */
    public function requestShouldReturnResponseWithCorrectStructure(ApiTester $I): void
    {
        $I->sendPOST($this->url, ['username' => 'john@econumo.test', 'code' => '39e83221911d', 'password' => 'pass']);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseMatchesJsonType(['data' => [],]);
    }
}
