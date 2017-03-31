<?php

namespace Aaronadal\ConfigBundle\DependencyInjection;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\ClosureLoader;
use Symfony\Component\DependencyInjection\Loader\DirectoryLoader;
use Symfony\Component\DependencyInjection\Loader\IniFileLoader;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
class LoadConfigCompilerPass implements CompilerPassInterface
{

    /**
     * Returns a file loader
     *
     * @param ContainerBuilder $container
     *
     * @return Loader
     */
    private function getLoader(ContainerBuilder $container)
    {
        $locator = new FileLocator();
        $resolver = new LoaderResolver(array(
            new XmlFileLoader($container, $locator),
            new YamlFileLoader($container, $locator),
            new IniFileLoader($container, $locator),
            new PhpFileLoader($container, $locator),
            new DirectoryLoader($container, $locator),
            new ClosureLoader($container),
        ));

        return new DelegatingLoader($resolver);
    }

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $loader = $this->getLoader($container);

        // Load the configuration files inside the defaults location.
        $defaultsLocation = $container->getParameter('aaronadal.config.location.defaults');
        foreach (glob($defaultsLocation) as $file) {
            $loader->load($file);
        }

        // Load the configuration files inside the environment location.
        $environmentLocation = $container->getParameter('aaronadal.config.location.environment');
        foreach (glob($environmentLocation) as $file) {
            $loader->load($file);
        }
    }

}
