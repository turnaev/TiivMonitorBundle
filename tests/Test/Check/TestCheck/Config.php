<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Test\Check\TestCheck;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Tvi\MonitorBundle\Check\CheckConfigAbstract;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Config extends CheckConfigAbstract
{
    public const DESCR =
<<<'TXT'
        test_check description
TXT;

    public const PATH = __DIR__;

    public const GROUP = 'test';
    public const CHECK_NAME = 'test_check';

    /**
     * @param NodeDefinition|ArrayNodeDefinition $node
     */
    protected function _check(NodeDefinition $node): NodeDefinition
    {
        $node = $node
            ->children()
                ->arrayNode('check')
                    ->children()
                    ->end()
                ->end()
            ->end()
        ;

        $this->_group($node);
        $this->_tags($node);
        $this->_label($node);

        return $node;
    }
}
