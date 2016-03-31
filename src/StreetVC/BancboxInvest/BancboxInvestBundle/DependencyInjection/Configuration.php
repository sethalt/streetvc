<?php

namespace StreetVC\BancboxInvest\BancboxInvestBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('bancbox_invest');

        $rootNode
            ->children()
                ->scalarNode('api_key')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('secret')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('baseUrl')->defaultValue('https://sandbox-api.bancboxcrowd.com/crowd/v0/cfp/')->end()
                ->scalarNode('created_by')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('cfp_id')->isRequired()->cannotBeEmpty()->end()
                ->arrayNode('mappings')
                    ->useAttributeAsKey('command')
                    ->prototype('array')
                        ->children()
                            ->arrayNode('request')
                                ->useAttributeAsKey('name')
                                ->prototype('scalar')->end()
                            ->end()
                            ->arrayNode('response')
                                ->useAttributeAsKey('name')
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
