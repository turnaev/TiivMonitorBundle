<?php

namespace Tvi\MonitorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Tvi\MonitorBundle\Check\CheckFinder;

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
     * @var string[]
     */
    private $checksSearchPaths = [];

    /**
     * @var array
     */
    private $checkMatadatas = [];

    /**
     * Configuration constructor.
     *
     * @param string[]|null $checksSearchPaths
     */
    public function __construct(array $checksSearchPaths = null)
    {
        $this->checksSearchPaths = $checksSearchPaths ? $checksSearchPaths : [];
    }

    /**
     * @return array
     */
    public function getCheckMatadatas(): array
    {
        return $this->checkMatadatas;
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

        $configurationClasses = $this->getConfigClasses();

        $addChecks = function($rootNode) use($configurationClasses, $builder) {

            foreach ($configurationClasses as $conf) {

                $conf = new $conf();
                foreach (get_class_methods($conf) as $method) {
                    /* @var \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node */
                    $node = $conf->$method($builder);
                    $checkName = $node->getNode(true)->getName();
                    $factoryServiceName = preg_replace('/_factory$/', '', $checkName);
                    $this->checkMatadatas[$checkName] = ['path' =>$conf::PATH, 'conf' =>'check.yml', 'service' =>$factoryServiceName];
                    $rootNode->append($node);
                }
            }
            return $rootNode;
        };

        $node = $builder
            ->root('checks', 'array')
            ->beforeNormalization()
            ->always(function ($value) {
                foreach ($value as $k=>$v) {
                    $newK = str_replace('(s)', '_factory', $k);
                    if($newK != $k) {
                        $value[$newK] = $value[$k];
                        unset($value[$k]);
                    }
                }
                return $value;
            })->end()
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
            ->/** @scrutinizer ignore-call */prototype('scalar')->end();
    }

    private function getConfigClasses()
    {
        $checkFinder = new CheckFinder($this->checksSearchPaths);
        return $checkFinder->find();
    }
}
