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

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Tvi\MonitorBundle\Exception\FeatureRequired;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
abstract class CheckPluginAbstract implements CheckPluginInterface
{
    /**
     * @throws FeatureRequired
     */
    public function checkRequirements(array $checkSettings)
    {
    }

    /**
     * @return NodeDefinition|ArrayNodeDefinition
     */
    public function checkConf(TreeBuilder $builder): NodeDefinition
    {
        $node = $builder
            ->root(static::CHECK_NAME, 'array')
            ->info(static::DESCR); //--

        $this->_check($node);

        return $node;
    }

    /**
     * @return NodeDefinition|ArrayNodeDefinition
     */
    public function checkFactoryConf(TreeBuilder $builder): NodeDefinition
    {
        $node = $builder
            ->root(static::CHECK_NAME.'_factory', 'array')
            ->info(static::DESCR)
            ->children()
                ->arrayNode('items')
                    ->prototype('array'); //--
        $node = $this->_check($node)
                    ->end()
                ->end()
            ->end();

        $this->_addition($node);

        return $node;
    }

    /**
     * @param NodeDefinition|ArrayNodeDefinition $node
     *
     * @return NodeDefinition|ArrayNodeDefinition
     */
    protected function _addition(NodeDefinition $node): NodeDefinition
    {
        $this->_group($node);
        $this->_tags($node);
        $this->_importance($node);
        $this->_label($node);
        $this->_descr($node);


        return $node;
    }

    /**
     * @param NodeDefinition|ArrayNodeDefinition $node
     *
     * @return NodeDefinition|ArrayNodeDefinition
     */
    abstract protected function _check(NodeDefinition $node): NodeDefinition;

    /**
     * @param NodeDefinition|ArrayNodeDefinition $node
     *
     * @return NodeDefinition|ArrayNodeDefinition
     */
    protected function _label(NodeDefinition $node): NodeDefinition
    {
        return $node
            ->children()
                ->scalarNode('label')->defaultNull()->end()
            ->end();
    }

    /**
     * @param NodeDefinition|ArrayNodeDefinition $node
     *
     * @return NodeDefinition|ArrayNodeDefinition
     */
    protected function _importance(ArrayNodeDefinition $node): NodeDefinition
    {
        return $node
            ->children()
                ->scalarNode('importance')
                    ->validate()
                        ->ifTrue(static function ($value) {
                            if (null === $value) {
                                return false;
                            } else if (\in_array($value, CheckAbstract::getImportances(), true)) {
                                return false;
                            }
                            return true;
                        })
                        ->thenInvalid(sprintf('importance has to one of value [%s]', implode(', ', CheckAbstract::getImportances())))
                    ->end()
                    ->defaultNull()
                ->end()
            ->end();
    }

    /**
     * @param NodeDefinition|ArrayNodeDefinition $node
     *
     * @return NodeDefinition|ArrayNodeDefinition
     */
    protected function _group(NodeDefinition $node): NodeDefinition
    {
        return $node
            ->children()
                ->scalarNode('group')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('_group')
                    ->defaultValue(static::GROUP)
                    ->cannotBeOverwritten()
                ->end()
            ->end();
    }

    /**
     * @param NodeDefinition|ArrayNodeDefinition $node
     *
     * @return NodeDefinition|ArrayNodeDefinition
     */
    protected function _tags(NodeDefinition $node): NodeDefinition
    {
        return $node
            ->children()
                ->arrayNode('tags')
                    ->prototype('scalar')->end()
                ->end()
            ->end();
    }

    /**
     * @param NodeDefinition|ArrayNodeDefinition $node
     *
     * @return NodeDefinition|ArrayNodeDefinition
     */
    protected function _descr(NodeDefinition $node): NodeDefinition
    {
        return $node
            ->children()
                ->scalarNode('descr')
                    ->defaultNull()
                ->end()
            ->end();
    }
}
