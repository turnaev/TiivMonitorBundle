<?php

namespace Tvi\MonitorBundle\Check\PhpExtension;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Tvi\MonitorBundle\Check\AbstarctConfiguration;

class Configuration extends AbstarctConfiguration
{
    const PATH = __DIR__;

    const GROUP = 'php';
    const DESCR = 'Validate that a named extension or a collection of extensions is available';
    const EXAMPLE = '["apc"] or "apc"';

    private function __check(ArrayNodeDefinition $node): ArrayNodeDefinition
    {
        $node = $node
            ->example(static::EXAMPLE)
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
            ->end();

        $this->__group($node);
        $this->__tags($node);
        $this->__label($node);

        return $node;
    }

    public function check(TreeBuilder $builder): ArrayNodeDefinition
    {
        $node = $builder
            ->root('php_extension', 'array')
            ->info(static::DESCR); //--
            $this->__check($node);

        return $node;
    }

    public function check_collection(TreeBuilder $builder): ArrayNodeDefinition
    {
        $node = $builder
            ->root('php_extension_collection', 'array')
            ->info(static::DESCR)
            ->children()
                ->arrayNode('items')
                    ->useAttributeAsKey('name')
                    ->prototype('array'); //--
                        $node = $this->__check($node)
                    ->end()
                ->end()
            ->end();

        $this->__group($node);
        $this->__tags($node);
        $this->__label($node);

        return $node;
    }
}
