<?php

namespace SergioTropea\NexiBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class SergioTropeaNexiExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('sergio_tropea_nexi.alias', $config['alias']);
        $container->setParameter('sergio_tropea_nexi.key', $config['key']);
        $container->setParameter('sergio_tropea_nexi.url', $config['url']);
        $container->setParameter('sergio_tropea_nexi.environment', $config['environment']);

    }
}
