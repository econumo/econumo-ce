<?php

namespace App\Tests\api;

use App\Tests\ApiTester;
use Codeception\Util\HttpCode;

class SwaggerCest
{
    public function shouldApiDocReturn200(ApiTester $I): void
    {
        $I->sendGET('/api/doc');
        $I->canSeeResponseCodeIs(HttpCode::OK);
    }

    public function shouldApiDocJsonReturn200(ApiTester $I): void
    {
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');
        $I->sendGET('/api/doc.json');
        $I->canSeeResponseCodeIs(HttpCode::OK);
    }
}
