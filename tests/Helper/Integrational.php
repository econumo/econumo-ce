<?php

namespace App\Tests\Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I


use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use Ramsey\Uuid\Uuid;

class Integrational extends \Codeception\Module
{
    use ContainerTrait;

    /**
     * @return \App\EconumoBundle\Domain\Entity\ValueObject\Id
     * @throws \Exception
     */
    public function generateId(): Id
    {
        $uuid = Uuid::uuid4();

        return new Id($uuid->toString());
    }
}
