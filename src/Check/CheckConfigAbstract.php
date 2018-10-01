<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
abstract class CheckConfigAbstract implements CheckConfigInterface
{
    public function check(TreeBuilder $builder): NodeDefinition
    {
        $node = $builder
            ->root(static::CHECK_NAME, 'array')
            ->info(static::DESCR) //--
        ;
        $this->_check($node);

        return $node;
    }

    public function check_factory(TreeBuilder $builder): NodeDefinition
    {
        $node = $builder
            ->root(static::CHECK_NAME.'_factory', 'array')
            ->info(static::DESCR)
            ->children()
                ->arrayNode('items')
                    ->useAttributeAsKey('key')
                    ->prototype('array') //--
        ;
        $node = $this->_check($node)
                    ->end()
                ->/* @scrutinizer ignore-call */ end()
            ->end()
                ;

        $this->_group($node);
        $this->_tags($node);
        $this->_label($node);

        return $node;
    }

    abstract protected function _check(NodeDefinition $node): NodeDefinition;

    protected function _label(NodeDefinition $node): NodeDefinition
    {
        return $node
            ->children()
                ->scalarNode('label')->defaultNull()->end()
            ->end()
        ;
    }

    protected function _group(NodeDefinition $node): NodeDefinition
    {
        return $node
            ->children()
                ->scalarNode('group')
                    ->defaultValue(static::GROUP)
                    ->cannotBeEmpty()
                ->end()
            ->end()
        ;
    }

    protected function _tags(NodeDefinition $node): NodeDefinition
    {
        return $node
            ->children()
                ->arrayNode('tags')
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ;
    }
}
