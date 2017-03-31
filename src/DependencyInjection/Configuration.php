<?php

namespace Aaronadal\ConfigBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('aaronadal_config');

        $rootNode
            ->children()
                ->arrayNode('location')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('defaults')
                            ->defaultValue('config/parameters/defaults/*')
                        ->end()
                        ->scalarNode('environment')
                            ->defaultValue('config/parameters/:env/*')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
