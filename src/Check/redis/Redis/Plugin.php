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
use Tvi\MonitorBundle\Check\CheckPluginAbstract;
use Tvi\MonitorBundle\Exception\FeatureRequired;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Plugin extends CheckPluginAbstract
{
    const DESCR =
<<<'TXT'
redis description
TXT;

    const PATH = __DIR__;

    const GROUP = 'redis';
    const CHECK_NAME = 'core:redis';

    /**
     * @throws FeatureRequired
     */
    public function checkRequirements(array $checkSettings)
    {
        if (!class_exists('Predis\Client')) {
            throw new FeatureRequired('The predis/predis is required for '.static::class.' check.');
        }
    }

    protected function _check(ArrayNodeDefinition $node): ArrayNodeDefinition
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
