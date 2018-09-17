<?php

namespace Tvi\MonitorBundle\Check\PhpVersion;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Tvi\MonitorBundle\Check\AbstarctConfiguration;

class Configuration extends AbstarctConfiguration
{
    const PATH = __DIR__;

    const GROUP = 'php';
    const DESCR = 'Pairs of a version and a comparison operator';
    const EXAMPLE = '{expectedVersion: "5.4.15", operator: ">="}';

    private function __check(ArrayNodeDefinition $node): ArrayNodeDefinition
    {
        return $node
            ->example(static::EXAMPLE)
            ->children()
                ->scalarNode('expectedVersion')->isRequired()->end()
                ->scalarNode('operator')->defaultValue('>=')->end()
            ->end();
    }

    public function check(TreeBuilder $builder): ArrayNodeDefinition
    {
        $node = $builder
            ->root('php_version', 'array')
            ->info(static::DESCR); //--
            $this->__check($node);

        $this->__group($node);
        $this->__tags($node);
        $this->__label($node);

        return $node;
    }

    public function check_collection(TreeBuilder $builder): ArrayNodeDefinition
    {
        $node = $builder
            ->root('php_version_collection', 'array')
            ->info(static::DESCR)
            ->children()
                ->arrayNode('items')
                    ->useAttributeAsKey('name')
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
}
