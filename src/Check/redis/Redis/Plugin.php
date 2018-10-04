<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\redis\Redis;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Tvi\MonitorBundle\Check\CheckPluginAbstract;
use Tvi\MonitorBundle\Exception\FeatureRequired;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Plugin extends CheckPluginAbstract
{
    public const DESCR =
<<<'TXT'
redis description
TXT;

    public const PATH = __DIR__;

    public const GROUP = 'redis';
    public const CHECK_NAME = 'core:redis';

    /**
     * @throws FeatureRequired
     */
    public function checkRequirements()
    {
        if (!class_exists('Predis\Client')) {
            throw new FeatureRequired('The predis/predis is required for '.static::class.' check.');
        }
    }

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
                        ->scalarNode('host')->defaultValue('localhost')->end()
                        ->integerNode('port')->defaultValue(6379)->end()
                        ->scalarNode('auth')->defaultValue(null)->end()
                    ->end()
                ->end()
            ->end();

        $this->_addition($node);

        return $node;
    }
}
