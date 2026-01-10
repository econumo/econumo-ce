<?php

declare(strict_types=1);

namespace App\Tests\api\v1\user;

use App\Tests\ApiTester;
use Codeception\Util\HttpCode;

class UpdatePasswordCest
{
    private string $url = '/api/v1/user/update-password';

    /**
     * @throws \Codeception\Exception\ModuleException
     * @skip
     */
    public function requestShouldReturn200ResponseCode(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, ['oldPassword' => 'pass', 'newPassword' => 'new_pass']);
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
    public function requestShouldReturn401ResponseCode(ApiTester $I): void
    {
        $I->sendPOST($this->url, ['oldPassword' => 'pass', 'newPassword' => 'new_pass']);
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     * @skip
     */
    public function requestShouldReturnResponseWithCorrectStructure(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, ['oldPassword' => 'pass', 'newPassword' => 'new_pass']);
        $I->seeResponseMatchesJsonType([
            'data' => [],
        ]);
    }
}
