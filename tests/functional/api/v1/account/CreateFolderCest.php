<?php

declare(strict_types=1);

namespace App\Tests\functional\api\v1\account;

use App\Tests\FunctionalTester;

class CreateFolderCest
{
    private string $url = '/api/v1/account/create-folder';

    public function shouldReturnCreatedFolderFromDB(FunctionalTester $I): void
    {
        $I->amAuthenticatedAsJohn();
        $I->sendPOST($this->url, ['name' => 'Family']);
        $I->seeRecord('folders', ['name' => 'Family', 'user_id' => 'aff21334-96f0-4fb1-84d8-0223d0280954']);
    }
}
