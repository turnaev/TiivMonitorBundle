<?php
/*
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\PhpExtension;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Tvi\MonitorBundle\Check\CheckConfigAbstract;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Config extends CheckConfigAbstract
{
    const PATH = __DIR__;

    const GROUP = 'php';
    const EXAMPLE = '["apc"] or "apc"';

    const CHECK_NAME = 'php_extension';

    protected function _check(NodeDefinition $node): NodeDefinition
    {
        $node = $node
            ->children()
                ->arrayNode('check')
                    ->beforeNormalization()
                    ->always(function ($value) {
                        if(isset($value['extensionName']) && !is_array($value['extensionName'])) {
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

        $this->_group($node);
        $this->_tags($node);
        $this->_label($node);

        return $node;
    }
}
