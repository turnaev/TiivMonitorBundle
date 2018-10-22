<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\rabbitmq\QueueSize;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Tvi\MonitorBundle\Check\CheckPluginAbstract;
use Tvi\MonitorBundle\Exception\FeatureRequired;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Plugin extends CheckPluginAbstract
{
    const DESCR =
<<<'TXT'
rabbit_mq description
TXT;

    const PATH = __DIR__;

    const GROUP = 'rabbit_mq';
    const CHECK_NAME = 'core:rabbit_mq:queue_size';

    /**
     * @throws FeatureRequired
     */
    public function checkRequirements(array $checkSettings)
    {
        if (!class_exists('PhpAmqpLib\Connection\AMQPConnection')) {
            throw new FeatureRequired('PhpAmqpLib is not installed');
        }
    }

    public function checkFactoryConf(TreeBuilder $builder): ArrayNodeDefinition
    {
        /* @var ArrayNodeDefinition $node */
        $node = parent::checkFactoryConf($builder);

        $node = $node
            ->beforeNormalization()
                ->ifArray()
                ->then(static function ($value) {
                    if (isset($value['dsn'])) {
                        foreach ($value['items'] as &$v) {
                            if (!array_key_exists('dsn', $v['check'])) {
                                $v['check']['dsn'] = $value['dsn'];
                            }
                        }
                    }

                    return $value;
                })
            ->end();

        $node->children()->scalarNode('dsn')->defaultNull()->end();

        return $node;
    }

    protected function _check(ArrayNodeDefinition $node): ArrayNodeDefinition
    {
        $node = $node
            ->children()
                ->arrayNode('check')
                    ->beforeNormalization()
                        ->ifString()
                        ->then(static function ($v) { return ['dsn' => $v]; })
                    ->end()
                    ->children()
                        ->scalarNode('queue')->isRequired()->end()
                        ->integerNode('warningThreshold')->defaultValue(null)->end()
                        ->integerNode('criticalThreshold')->defaultValue(100)->end()
                        ->scalarNode('host')->defaultValue('localhost')->end()
                        ->integerNode('port')->defaultValue(5672)->end()
                        ->scalarNode('user')->defaultValue('guest')->end()
                        ->scalarNode('password')->defaultValue('guest')->end()
                        ->scalarNode('vhost')->defaultValue('/')->end()
                        ->scalarNode('dsn')->defaultNull()->end()
                    ->end()
                ->end()
            ->end();

        $this->_addition($node);

        return $node;
    }
}
