<?php

namespace Tvi\MonitorBundle\Check;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

abstract class AbstarctConfiguration implements ConfigurationInterface
{
    abstract protected function __check(ArrayNodeDefinition $node): ArrayNodeDefinition;

    public function check(TreeBuilder $builder): ArrayNodeDefinition
    {
        $node = $builder
            ->root(static::CHECK_NAME, 'array')
            ->info(static::DESCR); //--
            $this->__check($node);

        return $node;
    }

    public function check_factory(TreeBuilder $builder): ArrayNodeDefinition
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
                ->end()
            ->end();

        $this->__group($node);
        $this->__tags($node);
        $this->__label($node);

        return $node;
    }

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
