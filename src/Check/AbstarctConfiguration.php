<?php

namespace Tvi\MonitorBundle\Check;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class AbstarctConfiguration implements ConfigurationInterface
{
    protected function __label(ArrayNodeDefinition $node): ArrayNodeDefinition
    {
        return $node
            ->children()
                ->scalarNode('label')->defaultNull()->end()
            ->end();
    }

    protected function __group(ArrayNodeDefinition $node): ArrayNodeDefinition
    {
        return $node
            ->children()
                ->scalarNode('group')
                    ->defaultValue(static::GROUP)
                    ->cannotBeEmpty()
                ->end()
            ->end();
    }

    protected function __tags(ArrayNodeDefinition $node): ArrayNodeDefinition
    {
        return $node
            ->children()
                ->arrayNode('tags')
                    ->prototype('scalar')->end()
                ->end()
            ->end();
    }
}
