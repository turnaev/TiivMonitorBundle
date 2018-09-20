<?php

namespace Tvi\MonitorBundle\Check\Fish;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Tvi\MonitorBundle\Check\CheckConfigAbstract;

class Config extends CheckConfigAbstract
{
    const PATH = __DIR__;

    const GROUP = 'fish';
    const DESCR = 'fish description';

    const CHECK_NAME = 'fish';

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
