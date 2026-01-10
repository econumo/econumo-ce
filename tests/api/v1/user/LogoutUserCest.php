<?php

declare(strict_types=1);

namespace App\Tests\api\v1\user;

use App\Tests\ApiTester;
use Codeception\Util\HttpCode;

class LogoutUserCest
{
    private string $url = '/api/v1/user/logout-user';
}
