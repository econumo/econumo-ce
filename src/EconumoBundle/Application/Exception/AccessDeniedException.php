<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Exception;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AccessDeniedException extends AccessDeniedHttpException
{
}
