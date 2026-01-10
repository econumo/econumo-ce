<?php

declare(strict_types=1);

namespace App\Tests\api\v1\category;

use App\Tests\ApiTester;
use Codeception\Util\HttpCode;

class DeleteCategoryCest
{
    private string $url = '/api/v1/category/delete-category';

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function requestShouldReturn200ResponseCodeOnDelete(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, ['id' => '95587d1d-2c39-4efc-98f3-23c755da44a4', 'mode' => 'delete']);
        $I->seeResponseCodeIs(HttpCode::OK);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function requestShouldReturn200ResponseCodeOnReplace(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, ['id' => '95587d1d-2c39-4efc-98f3-23c755da44a4', 'mode' => 'replace', 'replaceId' => 'ed547399-a380-43c9-b164-d8e435e043c9']);
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
        $I->sendPOST($this->url, ['id' => '95587d1d-2c39-4efc-98f3-23c755da44a4', 'mode' => 'delete']);
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function requestShouldReturnResponseWithCorrectStructureOnDelete(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, ['id' => '95587d1d-2c39-4efc-98f3-23c755da44a4', 'mode' => 'delete']);
        $I->seeResponseMatchesJsonType([
            'data' => [],
        ]);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function requestShouldReturnResponseWithCorrectStructureOnReplace(ApiTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, ['id' => '95587d1d-2c39-4efc-98f3-23c755da44a4', 'mode' => 'replace', 'replaceId' => 'ed547399-a380-43c9-b164-d8e435e043c9']);
        $I->seeResponseMatchesJsonType([
            'data' => [],
        ]);
    }
}
