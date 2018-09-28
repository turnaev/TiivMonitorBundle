<?php
/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\GuzzleHttpService;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Tvi\MonitorBundle\Check\CheckConfigAbstract;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Config extends CheckConfigAbstract
{
    const DESCR =
<<<TXT
        guzzle_http_service description
TXT;

    const PATH = __DIR__;

    const GROUP = 'http';
    const CHECK_NAME = 'guzzle_http_service';

    /**
     * @param NodeDefinition|ArrayNodeDefinition $node
     *
     * @return NodeDefinition
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
