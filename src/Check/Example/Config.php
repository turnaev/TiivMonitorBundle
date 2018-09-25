<?php

namespace Tvi\MonitorBundle\Check\Example;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Tvi\MonitorBundle\Check\CheckConfigAbstract;

class Config extends CheckConfigAbstract
{
    const PATH = __DIR__;

    const GROUP = 'example';
    const DESCR = 'example description';

    const CHECK_NAME = 'example';

    protected function _check(NodeDefinition $node): NodeDefinition
    {
        $node = $node
            ->children()
                ->arrayNode('check')
                    ->children()
                    ->end()
                ->end()
            ->end();

        $this->_group($node);
        $this->_tags($node);
        $this->_label($node);

        return $node;
    }
}
