<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Tvi\MonitorBundle\Check\CheckPluginFinder;

/**
 * This class contains the configuration information for the bundle.
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @var array
     */
    private $checkPlugins = [];

    private $checkPluginClasses = [];

    /**
     * Configuration constructor.
     */
    public function __construct(CheckPluginFinder $pluginFinder)
    {
        $this->checkPluginClasses = $pluginFinder->find();
    }

    public function getCheckPlugins(): array
    {
        return $this->checkPlugins;
    }

    /**
     * Generates the configuration tree.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $treeBuilder->root('tvi_monitor', 'array')
            ->children()
                ->append($this->addUIViewTemplate())
                ->append($this->addChecksSearchPaths())
                ->append($this->addGroups())
                ->append($this->addTags())
                ->append($this->addReporers())
                ->append($this->addChecks())
            ->end()
        ->end();

        return $treeBuilder;
    }

    private function addChecks(): ArrayNodeDefinition
    {
        $builder = new TreeBuilder();

        $checkPligins = $this->checkPlugins;

        $addChecks = function ($rootNode) use ($checkPligins, $builder) {
            foreach ($this->checkPluginClasses as $checkPluginClass) {
                $checkPligin = new $checkPluginClass();

                $confMethods = array_filter(get_class_methods($checkPligin), static function ($n) {
                    return preg_match('/Conf$/', $n);
                });

                foreach ($confMethods as $confMethod) {

                    /* @var ArrayNodeDefinition $node */
                    $node = $checkPligin->$confMethod($builder);
                    $checkName = $node->getNode(true)->getName();
                    $serviceName = preg_replace('/_factory$/', '', $checkName);

                    $this->checkPlugins[$checkName] = [
                        'checkServicePath' => $checkPligin::PATH.\DIRECTORY_SEPARATOR.'check.yml',
                        'service' => $serviceName,
                        'pligin' => $checkPligin,
                    ];

                    $rootNode->append($node);
                }
            }

            return $rootNode;
        };

        $node = $builder
            ->root('checks', 'array')
            ->beforeNormalization()
            ->always(static function ($value) {
                $value = $value ? $value : [];
                foreach ($value as $k => $v) {
                    $newK = str_replace('(s)', '_factory', $k);
                    if ($newK !== $k) {
                        $value[$newK] = $value[$k];
                        unset($value[$k]);
                    }
                }

                return $value;
            })
            ->end()
            ->children(); //--
        $node = $addChecks($node)
            ->end();

        return $node;
    }

    private function addReporers(): ArrayNodeDefinition
    {
        return (new TreeBuilder())
            ->root('reporters', 'array')
            ->children()
                ->arrayNode('mailer')
                    ->children()
                        ->scalarNode('recipient')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('sender')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('subject')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->booleanNode('send_on_warning')
                            ->defaultTrue()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    private function addTags(): ArrayNodeDefinition
    {
        return (new TreeBuilder())
            ->root('tags', 'array')
            ->prototype('array')
                ->children()
                    ->scalarNode('name')->end()
                    ->scalarNode('descr')->end()
                ->end()
            ->end();
    }

    private function addGroups(): ArrayNodeDefinition
    {
        return (new TreeBuilder())
            ->root('groups', 'array')
            ->prototype('array')
                ->children()
                    ->scalarNode('name')->end()
                    ->scalarNode('descr')->end()
                ->end()
            ->end();
    }

    private function addChecksSearchPaths(): ArrayNodeDefinition
    {
        return (new TreeBuilder())
            ->root('checks_search_paths', 'array')
            ->prototype('scalar')->end();
    }

    private function addUIViewTemplate()
    {
        return (new TreeBuilder())
            ->root('ui_view_template', 'scalar')
                ->cannotBeEmpty()
                ->defaultValue('@TviMonitor/UI/index.b4.html.twig');
    }
}
