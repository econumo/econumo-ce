<?php

declare(strict_types=1);

namespace App\Tests\api\v1\payee;

use App\Tests\ApiTester;
use Codeception\Util\HttpCode;

class OrderPayeeListCest
{
    private string $url = '/api/v1/payee/order-payee-list';

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function requestShouldReturn200ResponseCode(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, [
            'changes' => [
                [
                    'id' => '1d43b436-46cd-417e-abc4-6ec2a9bf99be',
                    'position' => 0
                ],
                [
                    'id' => '701ee173-7c7e-4f92-8af7-a27839c663e0',
                    'position' => 1
                ],
            ]
        ]);
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
        $I->sendPOST($this->url, [
            'changes' => [
                [
                    'id' => '1d43b436-46cd-417e-abc4-6ec2a9bf99be',
                    'position' => 0
                ],
                [
                    'id' => '701ee173-7c7e-4f92-8af7-a27839c663e0',
                    'position' => 1
                ],
            ]
        ]);
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function requestShouldReturnResponseWithCorrectStructure(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, [
            'changes' => [
                [
                    'id' => '1d43b436-46cd-417e-abc4-6ec2a9bf99be',
                    'position' => 0
                ],
                [
                    'id' => '701ee173-7c7e-4f92-8af7-a27839c663e0',
                    'position' => 1
                ],
            ]
        ]);
        $I->seeResponseMatchesJsonType($I->getRootResponseWithItemsJsonType());
        $I->seeResponseMatchesJsonType($I->getPayeeDtoJsonType(), '$.data.items[0]');
    }
}
