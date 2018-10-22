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

    public function checkConf(TreeBuilder $builder): ArrayNodeDefinition
    {
        $node = $builder
            ->root(static::CHECK_NAME, 'array')
            ->info(static::DESCR); //--

        $this->_check($node);

        return $node;
    }

    public function checkFactoryConf(TreeBuilder $builder): ArrayNodeDefinition
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

    protected function _addition(ArrayNodeDefinition $node): ArrayNodeDefinition
    {
        $this->_group($node);
        $this->_tags($node);
        $this->_importance($node);
        $this->_label($node);
        $this->_descr($node);

        return $node;
    }

    abstract protected function _check(ArrayNodeDefinition $node): ArrayNodeDefinition;

    protected function _label(ArrayNodeDefinition $node): ArrayNodeDefinition
    {
        return $node
            ->children()
                ->scalarNode('label')->defaultNull()->end()
            ->end();
    }

    protected function _importance(ArrayNodeDefinition $node): ArrayNodeDefinition
    {
        return $node
            ->children()
                ->scalarNode('importance')
                    ->validate()
                        ->ifTrue(static function ($value) {
                            if (null === $value) {
                                return false;
                            } elseif (\in_array($value, CheckAbstract::getImportances(), true)) {
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

    protected function _group(ArrayNodeDefinition $node): ArrayNodeDefinition
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

    protected function _tags(ArrayNodeDefinition $node): ArrayNodeDefinition
    {
        return $node
            ->children()
                ->arrayNode('tags')
                    ->prototype('scalar')->end()
                ->end()
            ->end();
    }

    protected function _descr(ArrayNodeDefinition $node): ArrayNodeDefinition
    {
        return $node
            ->children()
                ->scalarNode('descr')
                    ->defaultNull()
                ->end()
            ->end();
    }
}
