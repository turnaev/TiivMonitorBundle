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
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Tvi\MonitorBundle\Check\CheckConfigAbstract;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Config extends CheckConfigAbstract
{
    public const DESCR =
<<<'TXT'
guzzle_http_service description
TXT;

    public const PATH = __DIR__;

    public const GROUP = 'http';
    public const CHECK_NAME = 'guzzle_http_service';

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

        $this->_group($node);
        $this->_tags($node);
        $this->_label($node);

        return $node;
    }
}
