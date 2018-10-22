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
use Tvi\MonitorBundle\Check\CheckPluginAbstract;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Plugin extends CheckPluginAbstract
{
    const DESCR =
<<<'TXT'
php_extension description
TXT;

    const PATH = __DIR__;

    const GROUP = 'php';
    const CHECK_NAME = 'core:extension_loaded';

    protected function _check(ArrayNodeDefinition $node): ArrayNodeDefinition
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
