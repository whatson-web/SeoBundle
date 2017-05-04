<?php

namespace WH\SeoBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package WH\SeoBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        return $treeBuilder
            ->root('wh_seo', 'array')
            ->children()
            ->arrayNode('entities')
            ->prototype('array')
            ->children()
            ->arrayNode('urlFields')
            ->prototype('array')
            ->children()
            ->scalarNode('type')->end()
            ->scalarNode('string')->end()
            ->scalarNode('entity')->end()
            ->scalarNode('field')->end()
            ->scalarNode('prefix')->end()
            ->scalarNode('suffix')->end()
            ->end()
            ->end()
            ->end()
            ->arrayNode('defaultMetasFields')
            ->children()
            ->scalarNode('title')->end()
            ->scalarNode('description')->end()
            ->end()
            ->end()
            ->scalarNode('frontController')->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end();
    }
}
