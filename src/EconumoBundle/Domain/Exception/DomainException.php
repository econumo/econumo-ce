<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Exception;

use App\EconumoBundle\Domain\Exception\DomainExceptionInterface;
use DomainException as BaseDomainException;

class DomainException extends BaseDomainException implements DomainExceptionInterface
{

}
