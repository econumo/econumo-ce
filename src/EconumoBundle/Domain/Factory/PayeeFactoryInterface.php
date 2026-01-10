<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Factory;


use App\EconumoBundle\Domain\Entity\Payee;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\PayeeName;

interface PayeeFactoryInterface
{
    public function create(Id $userId, PayeeName $name): Payee;
}
