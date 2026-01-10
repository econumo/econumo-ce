<?php

declare(strict_types=1);

namespace App\Tests\api\v1\connection;

use App\Tests\ApiTester;
use Codeception\Util\HttpCode;

class AcceptInviteCest
{
    private string $url = '/api/v1/connection/accept-invite';

    /**
     * @throws \Codeception\Exception\ModuleException
     * @skip
     */
    public function requestShouldReturn200ResponseCode(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, ['code' => 'a1234']);
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
        $I->sendPOST($this->url, ['code' => 'a1234']);
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     * @skip
     */
    public function requestShouldReturnResponseWithCorrectStructure(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, ['code' => 'a1234']);
        $I->seeResponseMatchesJsonType($I->getRootResponseWithItemsJsonType());
        $I->seeResponseMatchesJsonType($I->getConnectionDtoJsonType(), '$.data.items[0]');
        $I->seeResponseMatchesJsonType($I->getConnectionAccountAccessDtoJsonType(), '$.data.items[0].sharedAccounts[0]');
    }
}
