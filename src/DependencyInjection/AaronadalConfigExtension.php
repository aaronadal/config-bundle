<?php

namespace Aaronadal\ConfigBundle\DependencyInjection;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
class AaronadalConfigExtension extends Extension
{

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

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

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }
}

