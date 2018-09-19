<?php

namespace Tvi\MonitorBundle\Check;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

abstract class AbstarctConfigurationCheck implements ConfigurationCheckInterface
{
    abstract protected function __check(NodeDefinition $node): NodeDefinition;

    public function check(TreeBuilder $builder): NodeDefinition
    {
        $node = $builder
            ->root(static::CHECK_NAME, 'array')
            ->info(static::DESCR); //--
            $this->__check($node);

        return $node;
    }

    public function check_factory(TreeBuilder $builder): NodeDefinition
    {
        $node = $builder
            ->root(static::CHECK_FACTORY_NAME, 'array')
            ->info(static::DESCR)
            ->children()
                ->arrayNode('items')
                    ->useAttributeAsKey('key')
                    ->prototype('array'); //--
                        $node = $this->__check($node)
                    ->end()
                ->/** @scrutinizer ignore-call */ end()
            ->end();

        $this->__group($node);
        $this->__tags($node);
        $this->__label($node);

        return $node;
    }

    protected function __label(NodeDefinition $node): NodeDefinition
    {
        return $node
            ->children()
                ->scalarNode('label')->defaultNull()->end()
            ->end();
    }

    protected function __group(NodeDefinition $node): NodeDefinition
    {
        return $node
            ->children()
                ->scalarNode('group')
                    ->defaultValue(static::GROUP)
                    ->cannotBeEmpty()
                ->end()
            ->end();
    }

    protected function __tags(NodeDefinition $node): NodeDefinition
    {
        return $node
            ->children()
                ->arrayNode('tags')
                    ->prototype('scalar')->end()
                ->end()
            ->end();
    }
}
