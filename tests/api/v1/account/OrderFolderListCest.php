<?php

declare(strict_types=1);

namespace App\Tests\api\v1\account;

use App\Tests\ApiTester;
use Codeception\Util\HttpCode;

class OrderFolderListCest
{
    private string $url = '/api/v1/account/order-folder-list';

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function requestShouldReturn200ResponseCode(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, [
            'changes' => [
                [
                    'id' => '1ad16d32-36af-496e-9867-3919436b8d86',
                    'position' => 1
                ],
                [
                    'id' => '226557ac-7741-455b-b51d-6d038fe1ae1a',
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
                    'id' => '1ad16d32-36af-496e-9867-3919436b8d86',
                    'position' => 1
                ],
                [
                    'id' => '226557ac-7741-455b-b51d-6d038fe1ae1a',
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
                    'id' => '1ad16d32-36af-496e-9867-3919436b8d86',
                    'position' => 1
                ],
                [
                    'id' => '226557ac-7741-455b-b51d-6d038fe1ae1a',
                    'position' => 0
                ],
            ]
        ]);
        $I->seeResponseMatchesJsonType($I->getRootResponseWithItemsJsonType());
        $I->seeResponseMatchesJsonType($I->getAccountFolderDtoJsonType(), '$.data.items[0]');
    }
}
