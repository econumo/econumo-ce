<?php

declare(strict_types=1);

namespace App\Tests\api\v1\system;

use Codeception\Exception\ModuleException;
use App\Tests\ApiTester;
use Codeception\Util\HttpCode;

class ImportCurrencyRatesCest
{
    private string $url = '/api/v1/system/import-currency-rates';

    /**
     * @throws ModuleException
     */
    public function requestShouldReturn200ResponseCode(ApiTester $I): void
    {
        $I->amAuthenticatedAsSystemUser();
        $I->sendPOST($this->url,
            [
                'timestamp' => '1694887205',
                'base' => 'USD',
                'items' => [['code' => 'EUR', 'rate' => '1.2']]
            ]
        );
        $I->seeResponseCodeIs(HttpCode::OK);
    }

    /**
     * @throws ModuleException
     */
    public function requestShouldReturn400ResponseCode(ApiTester $I): void
    {
        $I->amAuthenticatedAsSystemUser();
        $I->sendPOST($this->url, ['unexpected_param' => 'test']);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    /**
     * @throws ModuleException
     */
    public function requestShouldReturn403ResponseCode(ApiTester $I): void
    {
        $I->sendPOST($this->url,
            [
                'timestamp' => '1694887205',
                'base' => 'USD',
                'items' => [['code' => 'EUR', 'rate' => '1.2']]
            ]
        );
        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);
    }

    /**
     * @throws ModuleException
     */
    public function requestShouldReturn403ResponseCode2(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url,
            [
                'timestamp' => '1694887205',
                'base' => 'USD',
                'items' => [['code' => 'EUR', 'rate' => '1.2']]
            ]
        );
        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);
    }

    /**
     * @throws ModuleException
     */
    public function requestShouldReturnResponseWithCorrectStructure(ApiTester $I): void
    {
        $I->amAuthenticatedAsSystemUser();
        $I->sendPOST($this->url,
            [
                'timestamp' => '1694887205',
                'base' => 'USD',
                'items' => [['code' => 'EUR', 'rate' => '1.2']]
            ]
        );
        $I->seeResponseMatchesJsonType(['data' => []]);
    }
}
