<?php

declare(strict_types=1);

namespace App\EconumoToolsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class EconumoToolsBundle extends Bundle
{
    public function isActive(): bool
    {
        return !file_exists(__DIR__.'/.disabled');
    }
}
