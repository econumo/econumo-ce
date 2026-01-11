<?php

declare(strict_types=1);

namespace App\Tests\api\v1\user;

use App\Tests\ApiTester;
use Codeception\Util\HttpCode;

class LoginUserCest
{
//    private string $url = '/api/v1/user/login-user';
//
//    /**
//     * @throws \Codeception\Exception\ModuleException
//     */
//    public function requestShouldReturn200ResponseCode(ApiTester $I): void
//    {
//        $I->sendPOST($this->url, ['username' => 'john@econumo.test', 'password' => 'pass',]);
//        $I->seeResponseCodeIs(HttpCode::OK);
//    }
//
//    /**
//     * @throws \Codeception\Exception\ModuleException
//     */
//    public function requestShouldReturn400ResponseCode(ApiTester $I): void
//    {
//        $I->sendPOST($this->url, ['username' => 'john@econumo.test', 'password' => 'pass', 'id' => 'test',]);
//        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
//    }
//
//    /**
//     * @throws \Codeception\Exception\ModuleException
//     */
//    public function requestShouldReturn401ResponseCode(ApiTester $I): void
//    {
//        $I->sendPOST($this->url, ['username' => 'john@econumo.test', 'password' => 'wrong',]);
//        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
//    }
//
//    /**
//     * @throws \Codeception\Exception\ModuleException
//     */
//    public function requestShouldReturnResponseWithCorrectStructure(ApiTester $I): void
//    {
//        $I->sendPOST($this->url, ['username' => 'john@econumo.test', 'password' => 'pass',]);
//        $I->seeResponseMatchesJsonType([
//            'token' => 'string',
//            'user' => $I->getCurrentUserDtoJsonType(),
//        ]);
//    }
}
