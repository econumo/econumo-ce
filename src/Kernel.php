<?php

namespace App;

use App\EconumoBundle\Domain\Service\EventHandlerInterface;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    /**
     * @var string
     */
    private const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    /**
     * @var Bundle[]
     */
    private array $econumoBundles = [];

    public function registerBundles(): iterable
    {
        $contents = require $this->getProjectDir().'/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                yield new $class();
            }
        }

        foreach ($this->getEconumoBundles() as $className) {
            $bundle = new $className();
            if (method_exists($bundle, 'isActive') && $bundle->isActive()) {
                $this->econumoBundles[$className] = $bundle;
                yield $bundle;
            }
        }
    }

    public function getProjectDir(): string
    {
        return \dirname(__DIR__);
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->addResource(new FileResource($this->getProjectDir().'/config/bundles.php'));
        $container->setParameter('container.dumper.inline_class_loader', \PHP_VERSION_ID < 70400 || $this->debug);
        $container->setParameter('container.dumper.inline_factories', true);

        $confDir = $this->getProjectDir().'/config';

        $loader->load($confDir.'/{packages}/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{packages}/'.$this->environment.'/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{services}'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{services}_'.$this->environment.self::CONFIG_EXTS, 'glob');

        foreach ($this->econumoBundles as $bundle) {
            $loader->load($bundle->getPath() . '/Resources/config/{packages}/*' . self::CONFIG_EXTS, 'glob');
            $loader->load(
                $bundle->getPath() . '/Resources/config/{packages}/' . $this->environment . '/**/*' . self::CONFIG_EXTS,
                'glob'
            );
            $loader->load($bundle->getPath() . '/Resources/config/{services}' . self::CONFIG_EXTS, 'glob');
            $loader->load(
                $bundle->getPath() . '/Resources/config/{services}_' . $this->environment . self::CONFIG_EXTS,
                'glob'
            );
        }

        $container->registerForAutoconfiguration(EventHandlerInterface::class)->addTag('messenger.message_handler');
    }

    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
        $confDir = $this->getProjectDir().'/config';

        $routes->import($confDir.'/{routes}/'.$this->environment.'/*'.self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir.'/{routes}/*'.self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir.'/{routes}'.self::CONFIG_EXTS, '/', 'glob');

        foreach ($this->econumoBundles as $bundle) {
            $routes->import(
                $bundle->getPath() . '/Resources/config/{routes}/' . $this->environment . '/**/*' . self::CONFIG_EXTS,
                '/',
                'glob'
            );
            $routes->import($bundle->getPath() . '/Resources/config/{routes}/*' . self::CONFIG_EXTS, '/', 'glob');
            $routes->import($bundle->getPath() . '/Resources/config/{routes}' . self::CONFIG_EXTS, '/', 'glob');
        }
    }

    private function getEconumoBundles(): array
    {
        $bundlesPattern = $this->getProjectDir().'/src/*Bundle/*Bundle.php';
        $files = glob($bundlesPattern);
        $bundles = [];
        foreach ($files as $file) {
            $filename = pathinfo((string) $file, PATHINFO_FILENAME);
            $bundles[] = sprintf('App\%s\%s', $filename, $filename);
        }

        return $bundles;
    }
}
