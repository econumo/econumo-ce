<?php

declare(strict_types=1);

namespace App\Tests\api\v1\category;

use App\Tests\ApiTester;
use Codeception\Util\HttpCode;

class CreateCategoryCest
{
    private string $url = '/api/v1/category/create-category';

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function requestShouldReturn200ResponseCode(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, [
            'id' => '3a2c32a4-45ec-4cb0-9794-a6bef87ba9a4',
            'name' => 'Food',
            'type' => 'expense',
            'accountId' => '4eec1ee6-1992-4222-b9ab-31ece5eaad5d',
            'icon' => 'local_offer',
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function requestShouldReturn401ResponseCode(ApiTester $I): void
    {
        $I->sendPOST($this->url);
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
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
    public function requestShouldReturnResponseWithCorrectStructure(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, [
            'id' => '3a2c32a4-45ec-4cb0-9794-a6bef87ba9a4',
            'name' => 'Food',
            'type' => 'expense',
            'accountId' => '4eec1ee6-1992-4222-b9ab-31ece5eaad5d',
            'icon' => 'local_offer',
        ]);
        $I->seeResponseMatchesJsonType([
            'data' => [
                'item' => $I->getCategoryDtoJsonType(),
            ]
        ]);
    }
}
