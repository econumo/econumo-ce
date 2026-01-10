<?php

declare(strict_types=1);

namespace App\Tests\functional\api\v1\account;

use App\Tests\FunctionalTester;

class GetAccountListCest
{
    private string $url = '/api/v1/account/get-account-list';

    /**
     * @skip not working
     */
    public function shouldReturnCorrectAccounts(FunctionalTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendGET($this->url);
        $response = json_decode($I->grabResponse(), true);
        $actualIds = array_column($response['data']['items'], 'id');
        sort($actualIds);

        $expectedIds = [
            '4eec1ee6-1992-4222-b9ab-31ece5eaad5d',
            '5f3834d1-34e8-4f60-a697-004e63854513',
            '6c7b8af8-2f8a-4d6b-855c-ca6ff26952ff',
            '0aaa0450-564e-411e-8018-7003f6dbeb92',
        ];
        sort($expectedIds);
        $I->assertEquals($expectedIds, $actualIds);
    }
}
