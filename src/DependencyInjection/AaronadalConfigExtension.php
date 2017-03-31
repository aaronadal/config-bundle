<?php

namespace Aaronadal\ConfigBundle\DependencyInjection;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\ClosureLoader;
use Symfony\Component\DependencyInjection\Loader\DirectoryLoader;
use Symfony\Component\DependencyInjection\Loader\IniFileLoader;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
class AaronadalConfigExtension extends Extension
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

    private function setLocations(ContainerBuilder $container, $config)
    {
        $rootDir     = $container->getParameter('kernel.root_dir');
        $environment = $container->getParameter('kernel.environment');
        $filesystem  = new Filesystem();

        // Load the defaults location parameter.
        $defaultsLocation = $config['location']['defaults'];
        $defaultsLocation = str_replace(':env', $environment, $defaultsLocation);
        if(!$filesystem->isAbsolutePath($defaultsLocation)) {
            $defaultsLocation = $rootDir . DIRECTORY_SEPARATOR . $defaultsLocation;
        }
        $container->setParameter('aaronadal.config.location.defaults', $defaultsLocation);

        // Load the environment location parameter.
        $environmentLocation = $config['location']['environment'];
        $environmentLocation = str_replace(':env', $environment, $environmentLocation);
        if(!$filesystem->isAbsolutePath($environmentLocation)) {
            $environmentLocation = $rootDir . DIRECTORY_SEPARATOR . $environmentLocation;
        }
        $container->setParameter('aaronadal.config.location.environment', $environmentLocation);
    }

    private function loadConfiguration(ContainerBuilder $container)
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

    protected function getBundleResourcesDir()
    {
        return __DIR__ .  '/../Resources/config';
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $this->setLocations($container, $config);
        $this->loadConfiguration($container);

        $loader = new YamlFileLoader($container, new FileLocator($this->getBundleResourcesDir()));
        $loader->load('services.yml');
    }
}

