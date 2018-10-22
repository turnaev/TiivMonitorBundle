<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\etcd\Etcd;

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

    const GROUP = 'etcd';
    const CHECK_NAME = 'core:etcd';

    /**
     * @throws FeatureRequired
     */
    public function checkRequirements(array $checkSettings)
    {
        if (!interface_exists('\GuzzleHttp\ClientInterface')) {
            throw new FeatureRequired('GuzzleHttp is not installed');
        }
    }

    public function checkFactoryConf(TreeBuilder $builder): ArrayNodeDefinition
    {
        /* @var ArrayNodeDefinition $node */
        $node = parent::checkFactoryConf($builder);

        $keys = [
            'url',
            'verify',
            'cert',
            'sslKey',
            'ca',
        ];
        $node = $node
            ->beforeNormalization()
            ->ifArray()
            ->then(static function ($value) use ($keys) {
                foreach ($keys as $key) {
                    if (isset($value[$key])) {
                        foreach ($value['items'] as &$v) {
                            if (!array_key_exists($key, $v['check'])) {
                                $v['check'][$key] = $value[$key];
                            }
                        }
                    }
                }

                return $value;
            })
            ->end();

        $node->children()
            ->scalarNode('url')->end()
            ->booleanNode('verify')->end()
            ->scalarNode('cert')->end()
            ->scalarNode('sslKey')->end()
            ->scalarNode('ca')->end()
        ->end();

        return $node;
    }

    protected function _check(ArrayNodeDefinition $node): ArrayNodeDefinition
    {
        $node = $node
            ->children()
                ->arrayNode('check')
                    ->children()
                        ->scalarNode('url')->defaultValue('https://localhost:2379')->end()
                        ->booleanNode('verify')->defaultValue(false)->end()
                        ->scalarNode('cert')->defaultValue('/etc/etcd/cert/client-etcd.crt')->end()
                        ->scalarNode('sslKey')->defaultValue('/etc/etcd/cert/client-etcd.key')->end()
                        ->scalarNode('ca')->defaultValue('/etc/etcd/cert/ca.crt')->end()
                    ->end()
                ->end()
            ->end();

        $this->_addition($node);

        return $node;
    }
}
