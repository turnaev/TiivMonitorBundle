<?php

namespace Tvi\MonitorBundle\Check\PhpVersion;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Tvi\MonitorBundle\Check\CheckConfigAbstract;

class Config extends CheckConfigAbstract
{
    const PATH = __DIR__;

    const GROUP = 'php';
    const DESCR = 'Pairs of a version and a comparison operator';

    const CHECK_NAME = 'php_version';
    const CHECK_FACTORY_NAME = 'php_version_factory';

    protected function __check(NodeDefinition $node): NodeDefinition
    {
        $node = $node
            ->children()
                ->arrayNode('check')
                    ->children()
                        ->scalarNode('expectedVersion')->isRequired()->end()
                        ->scalarNode('operator')->defaultValue('>=')->end()
                    ->end()
                ->end()
            ->end();

        $this->__group($node);
        $this->__tags($node);
        $this->__label($node);

        return $node;
    }
}
