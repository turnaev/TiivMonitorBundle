<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\php\ExtensionLoaded;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Tvi\MonitorBundle\Check\CheckPluginAbstract;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Plugin extends CheckPluginAbstract
{
    public const DESCR =
<<<'TXT'
php_extension description
TXT;

    public const PATH = __DIR__;

    public const GROUP = 'php';
    public const CHECK_NAME = 'core:extension_loaded';

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
                    ->beforeNormalization()
                    ->always(static function ($value) {
                        if (isset($value['extensionName']) && !\is_array($value['extensionName'])) {
                            $value['extensionName'] = [$value['extensionName']];
                        }

                        return $value;
                    })->end()
                    ->children()
                        ->arrayNode('extensionName')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        $this->_addition($node);

        return $node;
    }
}
