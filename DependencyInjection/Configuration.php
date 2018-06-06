<?php

namespace SergioTropea\NexiBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('sergio_tropea_nexi');

        $rootNode
            ->children()
                ->scalarNode('url')->end() //End Url
                ->scalarNode('alias')->end()
                ->scalarNode('key')->end()
                ->scalarNode('environment')->end()
            ->end()
    ;

        return $treeBuilder;
    }
}
