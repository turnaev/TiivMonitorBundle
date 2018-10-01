<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\fs\XmlFile;

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
        xml_file description
TXT;

    public const PATH = __DIR__;

    public const GROUP = 'fs';
    public const CHECK_NAME = 'xml_file';

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
                        ->arrayNode('files')
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
