<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\http\GuzzleHttpService;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Tvi\MonitorBundle\Check\CheckPluginAbstract;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Plugin extends CheckPluginAbstract
{
    const DESCR =
<<<'TXT'
guzzle_http_service description
TXT;

    const PATH = __DIR__;

    const GROUP = 'http';
    const CHECK_NAME = 'core:guzzle_http_service';

    protected function _check(ArrayNodeDefinition $node): ArrayNodeDefinition
    {
        $node = $node
            ->children()
                ->arrayNode('check')
                    ->children()
                        ->scalarNode('requestOrUrl')->defaultValue('localhost')->end()
                        ->variableNode('headers')->defaultValue([])->end()
                        ->variableNode('options')->defaultValue([])->end()
                        ->integerNode('statusCode')->defaultValue(200)->end()
                        ->scalarNode('method')->defaultValue('GET')->end()
                        ->scalarNode('content')->defaultNull()->end()
                        ->scalarNode('body')->defaultNull()->end()
                    ->end()
                ->end()
            ->end();

        $this->_addition($node);

        return $node;
    }
}
