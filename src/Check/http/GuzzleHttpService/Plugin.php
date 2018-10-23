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
guzzle_http_service description
TXT;

    const PATH = __DIR__;

    const GROUP = 'http';
    const CHECK_NAME = 'core:guzzle_http_service';

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
            'headers',
            'options',
            'statusCode',
            'method',
            'content',
            'body',
            'withData',
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
            ->variableNode('headers')->end()
            ->variableNode('options')->end()
            ->integerNode('statusCode')->end()
            ->scalarNode('method')->end()
            ->scalarNode('content')->end()
            ->scalarNode('body')->end()
            ->booleanNode('withData')->end()
        ->end();

        return $node;
    }

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
                        ->booleanNode('withData')->defaultFalse()->end()
                    ->end()
                ->end()
            ->end();

        $this->_addition($node);

        return $node;
    }
}
