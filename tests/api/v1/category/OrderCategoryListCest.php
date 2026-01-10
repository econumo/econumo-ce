<?php

declare(strict_types=1);

namespace App\Tests\api\v1\category;

use App\Tests\ApiTester;
use Codeception\Util\HttpCode;

class OrderCategoryListCest
{
    private string $url = '/api/v1/category/order-category-list';

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function requestShouldReturn200ResponseCode(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, [
            'changes' => [
                [
                    'id' => '95587d1d-2c39-4efc-98f3-23c755da44a4',
                    'position' => 1
                ],
                [
                    'id' => 'ed547399-a380-43c9-b164-d8e435e043c9',
                    'position' => 0
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
                    'id' => '95587d1d-2c39-4efc-98f3-23c755da44a4',
                    'position' => 1
                ],
                [
                    'id' => 'ed547399-a380-43c9-b164-d8e435e043c9',
                    'position' => 0
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
                    'id' => '95587d1d-2c39-4efc-98f3-23c755da44a4',
                    'position' => 1
                ],
                [
                    'id' => 'ed547399-a380-43c9-b164-d8e435e043c9',
                    'position' => 0
                ],
            ]
        ]);
        $I->seeResponseMatchesJsonType($I->getRootResponseWithItemsJsonType());
        $I->seeResponseMatchesJsonType($I->getCategoryDtoJsonType(), '$.data.items[0]');
    }
}
