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
                                ->booleanNode('tree')->defaultFalse()->end()
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
                                        ->arrayNode('title')
                                            ->prototype('array')
                                                ->children()
                                                    ->scalarNode('prefix')->end()
                                                    ->scalarNode('field')->end()
                                                    ->scalarNode('suffix')->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('description')
                                            ->prototype('array')
                                                ->children()
                                                    ->scalarNode('prefix')->end()
                                                    ->scalarNode('field')->end()
                                                    ->scalarNode('suffix')->end()
                                                ->end()
                                            ->end()
                                        ->end()
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