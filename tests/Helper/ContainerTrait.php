<?php

namespace App\Tests\Helper;

use Codeception\Exception\ModuleException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

trait ContainerTrait
{
    /**
     * @return ContainerInterface
     * @throws ModuleException
     */
    public function getServiceContainer(): ContainerInterface
    {
        return $this->getModule('Symfony')->grabService('test.service_container');
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     * @throws \Codeception\Exception\ModuleException
     */
    public function getAppContainer(): ContainerInterface
    {
        /** @var \Codeception\Module\Symfony $module */
        $module = $this->getModule('Symfony');

        return $module->kernel->getContainer();
    }

    public function getContainerService(string $service)
    {
        $container = $this->getServiceContainer();
        try {
            return $container->get($service);
        } catch (ServiceNotFoundException $e) {
            $container = $this->getAppContainer();
            return $container->get($service);
        }
    }
}
