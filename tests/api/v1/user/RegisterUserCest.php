<?php

declare(strict_types=1);

namespace App\Tests\api\v1\user;

use App\Tests\ApiTester;
use Codeception\Util\HttpCode;

class RegisterUserCest
{
    private string $url = '/api/v1/user/register-user';

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function requestShouldReturn200ResponseCode(ApiTester $I): void
    {
        $I->sendPOST($this->url, ['email' => 'sersei@lannister.test', 'password' => 'pass', 'name' => 'Sersei']);
        $I->seeResponseCodeIs(HttpCode::OK);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function requestShouldReturn400ResponseCode(ApiTester $I): void
    {
        $I->sendPOST($this->url, ['email' => 'sersei']);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseMatchesJsonType([
            'message' => 'string',
            'code' => 'integer',
            'errors' => [
                'email' => ['string'],
                'password' => ['string'],
                'name' => ['string'],
            ],
        ]);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function requestShouldReturnResponseWithCorrectStructure(ApiTester $I): void
    {
        $I->sendPOST($this->url, ['email' => 'sersei@lannister.test', 'password' => 'pass', 'name' => 'Sersei']);
        $I->seeResponseMatchesJsonType([
            'data' => [
                'user' => $I->getCurrentUserDtoJsonType(),
            ],
        ]);
    }
}
