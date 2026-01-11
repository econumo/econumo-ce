<?php

declare(strict_types=1);

namespace App\Tests\api\v1\user;

use Codeception\Exception\ModuleException;
use App\Tests\ApiTester;
use Codeception\Util\HttpCode;

class UpdateBudgetCest
{
    private string $url = '/api/v1/user/update-budget';

    /**
     * @throws ModuleException
     * @skip
     */
    public function requestShouldReturn200ResponseCode(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, ['value' => '3a6d84be-d074-4a14-ab9a-86dfb083c91d']);
        $I->seeResponseCodeIs(HttpCode::OK);
    }

    /**
     * @throws ModuleException
     */
    public function requestShouldReturn400ResponseCode(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, ['unexpected_param' => 'test']);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    /**
     * @throws ModuleException
     */
    public function requestShouldReturn401ResponseCode(ApiTester $I): void
    {
        $I->sendPOST($this->url, ['value' => '3a6d84be-d074-4a14-ab9a-86dfb083c91d']);
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }

    /**
     * @throws ModuleException
     * @skip
     * @
     */
    public function requestShouldReturnResponseWithCorrectStructure(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, ['value' => '3a6d84be-d074-4a14-ab9a-86dfb083c91d']);
        $I->seeResponseMatchesJsonType([
            'data' => [
                'user' => $I->getCurrentUserDtoJsonType(),
            ],
        ]);
    }
}
