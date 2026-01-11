<?php

declare(strict_types=1);

namespace App\Tests\api\v1\connection;

use App\Tests\ApiTester;
use Codeception\Util\HttpCode;

class DeleteConnectionCest
{
    private string $url = '/api/v1/connection/delete-connection';

    /**
     * @throws \Codeception\Exception\ModuleException
     * @skip Not working
     */
    public function requestShouldReturn200ResponseCode(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, ['id' => '77be9577-147b-4f05-9aa7-91d9b159de5b']);
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
        $I->sendPOST($this->url, ['id' => '77be9577-147b-4f05-9aa7-91d9b159de5b']);
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     * @skip Not working
     */
    public function requestShouldReturnResponseWithCorrectStructure(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, ['id' => '77be9577-147b-4f05-9aa7-91d9b159de5b']);
        $I->seeResponseMatchesJsonType([
            'data' => [],
        ]);
    }
}
