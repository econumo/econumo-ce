<?php

declare(strict_types=1);

namespace App\EconumoBundle\Infrastructure\Doctrine\Type;

use ReflectionException;
use ReflectionClass;

trait ReflectionValueObjectTrait
{
    /**
     * @param $value
     * @return mixed
     * @throws ReflectionException
     */
    public function getInstance(string $className, $value)
    {
        $reflection = new ReflectionClass($className);
        $instance = $reflection->newInstanceWithoutConstructor();
        $property = $reflection->getProperty('value');
        $property->setAccessible(true);
        $property->setValue($instance, $value);
        return $instance;
    }
}
