<?php

declare(strict_types=1);

namespace App\Tests\api\v1\payee;

use App\Tests\ApiTester;
use Codeception\Util\HttpCode;

class CreatePayeeCest
{
    private string $url = '/api/v1/payee/create-payee';

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function requestShouldReturn200ResponseCode(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, ['id' => 'a45cad64-52e8-11ec-bf63-0242ac130002', 'name' => 'Apple', 'accountId' => '4eec1ee6-1992-4222-b9ab-31ece5eaad5d']);
        $I->seeResponseCodeIs(HttpCode::OK);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function requestShouldReturn400ResponseCode(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, ['id' => 'a45cad64-52e8-11ec-bf63-0242ac130002', 'test' => 'Apple', 'accountId' => '4eec1ee6-1992-4222-b9ab-31ece5eaad5d']);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function requestShouldReturn401ResponseCode(ApiTester $I): void
    {
        $I->sendPOST($this->url, ['id' => 'a45cad64-52e8-11ec-bf63-0242ac130002', 'name' => 'Apple', 'accountId' => '4eec1ee6-1992-4222-b9ab-31ece5eaad5d']);
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function requestShouldReturnResponseWithCorrectStructure(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, ['id' => 'a45cad64-52e8-11ec-bf63-0242ac130002', 'name' => 'Apple', 'accountId' => '4eec1ee6-1992-4222-b9ab-31ece5eaad5d']);
        $I->seeResponseMatchesJsonType([
            'data' => [
                'item' => $I->getPayeeDtoJsonType(),
            ],
        ]);
    }
}
