<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\php\OpCacheMemory;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Tvi\MonitorBundle\Check\CheckPluginAbstract;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Plugin extends CheckPluginAbstract
{
    public const DESCR =
<<<'TXT'
op_cache_memory description
TXT;

    public const PATH = __DIR__;

    public const GROUP = 'php';
    public const CHECK_NAME = 'core:op_cache_memory';

    /**
     * @param NodeDefinition|ArrayNodeDefinition $node
     *
     * @return NodeDefinition|ArrayNodeDefinition
     */
    protected function _check(NodeDefinition $node): NodeDefinition
    {
        $node = $node
            ->children()
                ->arrayNode('check')
                    ->children()
                        ->integerNode('warningThreshold')->defaultValue(70)->end()
                        ->integerNode('criticalThreshold')->defaultValue(90)->end()
                    ->end()
                ->end()
            ->end();

        $this->_addition($node);

        return $node;
    }
}
