<?php

namespace StreetVC\BancboxInvest\BancboxInvestBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class StreetVCBancboxInvestExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        $container->setParameter('bancbox_invest.api_key', $config['api_key']);
        $container->setParameter('bancbox_invest.secret', $config['secret']);
        $container->setParameter('bancbox_invest.baseUrl', $config['baseUrl']);
        $container->setParameter('bancbox_invest.created_by', $config['created_by']);
        $container->setParameter('bancbox_invest.cfp_id', $config['cfp_id']);
        $container->setParameter('bancbox_invest.mappings', $config['mappings']);
    }

    public function getAlias()
    {
        return 'bancbox_invest';
    }
}
