<?php

namespace Tvi\MonitorBundle\Check\PhpExtension;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Tvi\MonitorBundle\Check\CheckConfigAbstract;

class Config extends CheckConfigAbstract
{
    const PATH = __DIR__;

    const GROUP = 'php';
    const DESCR = 'Validate that a named extension or a collection of extensions is available';
    const EXAMPLE = '["apc"] or "apc"';

    const CHECK_NAME = 'php_extension';
    const CHECK_FACTORY_NAME = 'php_extension_factory';

    protected function __check(NodeDefinition $node): NodeDefinition
    {
        $node = $node
            ->example(static::EXAMPLE)
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

        $this->__group($node);
        $this->__tags($node);
        $this->__label($node);

        return $node;
    }
}
