<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\php\ClassExists;

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
class_exists description
TXT;

    public const PATH = __DIR__;

    public const GROUP = 'php';
    public const CHECK_NAME = 'core:class_exists';

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
                        ->arrayNode('classNames')
                            ->isRequired()
                            ->beforeNormalization()
                                ->ifString()
                                ->then(static function ($value) {
                                    if (\is_string($value)) {
                                        $value = [$value];
                                    }

                                    return $value;
                                })
                                ->end()
                            ->prototype('scalar')->end()
                        ->end()
                        ->booleanNode('autoload')->defaultTrue()->end()
                    ->end()
                ->end()
            ->end();

        $this->_addition($node);

        return $node;
    }
}
