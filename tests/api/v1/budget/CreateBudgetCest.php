<?php

declare(strict_types=1);

namespace App\Tests\api\v1\budget;

use Codeception\Exception\ModuleException;
use App\Tests\ApiTester;
use Codeception\Util\HttpCode;

class CreateBudgetCest
{
    private string $url = '/api/v1/budget/create-budget';

    /**
     * @throws ModuleException
     * @skip
     */
    public function requestShouldReturn200ResponseCode(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, [
            'id' => '9b29b760-ddca-46fb-a754-8743fc2c49a7',
            'name' => 'Personal Budget',
            'excludedAccounts' => ['5f3834d1-34e8-4f60-a697-004e63854513'],
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
    }

    /**
     * @throws ModuleException
     * @skip
     */
    public function requestShouldReturn400ResponseCode(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, ['unexpected_param' => 'test']);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    /**
     * @throws ModuleException
     * @skip
     */
    public function requestShouldReturn401ResponseCode(ApiTester $I): void
    {
        $I->sendPOST($this->url, ['id' => 'test']);
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }

    /**
     * @throws ModuleException
     * @skip
     */
    public function requestShouldReturnResponseWithCorrectStructure(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, [
            'id' => '9b29b760-ddca-46fb-a754-8743fc2c49a7',
            'name' => 'Personal Budget',
            'excludedAccounts' => ['5f3834d1-34e8-4f60-a697-004e63854513'],
        ]);
        $I->seeResponseMatchesJsonType([
            'data' => [
                'item' => $I->getBudgetDtoJsonType(),
            ],
        ]);
        $I->seeResponseMatchesJsonType($I->getBudgetEntityOptionDtoJsonType(), '$.data.item.entityOptions[0]');
    }
}
