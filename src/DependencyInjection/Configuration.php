<?php

namespace Tvi\MonitorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;


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
    private $checkPaths = [];

    public function __construct(array $checksSearchPaths = null)
    {
        $this->checksSearchPaths = $checksSearchPaths ? $checksSearchPaths : [];
    }

    /**
     * @return array
     */
    public function getCheckPaths(): array
    {
        return $this->checkPaths;
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
                ->append($this->addViewTemplate())
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

        $configurationClasses = $this->getConfigurationClasses();

        $paths = &$this->checkPaths;
        $addChecks = function($rootNode) use($configurationClasses, $builder, &$paths) {

            foreach ($configurationClasses as $conf) {

                $conf = new $conf();
                $methods = get_class_methods($conf);

                foreach ($methods as $method) {
                    /* @var \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node */
                    $node = $conf->$method($builder);
                    $paths[$node->getNode(true)->getName()] = [$conf::PATH, $method];
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
                    $newK = str_replace('(s)', '_collection', $k);
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
            ->useAttributeAsKey('name')
            ->beforeNormalization()
            ->always(function ($value) {
                foreach ($value as $tag=>&$v) {
                    if(empty($v['title'])) {
                        $v['title'] = $tag;
                    }
                }
                return $value;
            })
            ->end()
            ->prototype('array')
                ->children()
                    ->scalarNode('title')->end()
                    ->scalarNode('descr')->end()
                ->end()
            ->end();
    }

    private function addViewTemplate()
    {
        return (new TreeBuilder())
            ->root('view_template', 'scalar')
            ->defaultValue('@Tvi/MonitorBundle/Resources/views/ui/index.html.twig');
    }

    private function getConfigurationClasses()
    {
        $fetch =  function (&$tokens, $take) {
            $res = null;
            while ($token = current($tokens)) {
                list($token, $s) = is_array($token) ? $token : [$token, $token];
                if (in_array($token, (array) $take, true)) {
                    $res .= $s;
                } elseif (!in_array($token, [T_DOC_COMMENT, T_WHITESPACE, T_COMMENT], true)) {
                    break;
                }
                next($tokens);
            }
            return $res;
        };

        $fs = Finder::create();

        $configurationClasses = [];

        $dirs = [__DIR__.'/../Check/**/'];
        $dirs = array_merge($dirs, $this->checksSearchPaths);

        $files = $fs->in($dirs)->name('*.php')->files();

        foreach ($files as $f) {
            /* @var SplFileInfo $f */

            $tokens = @token_get_all($f->getContents());
            $namespace = $class = $classLevel = $level = null;

            while (list(, $token) = each($tokens)) {
                switch (is_array($token) ? $token[0] : $token) {
                    case T_NAMESPACE:
                        $namespace = ltrim($fetch($tokens, [T_STRING, T_NS_SEPARATOR]) . '\\', '\\');
                        break;
                    case T_CLASS:
                        if ($name = $fetch($tokens, T_STRING)) {
                            $configurationClass = '\\'.$namespace . $name;

                            if(is_subclass_of($configurationClass, \Tvi\MonitorBundle\Check\ConfigurationInterface::class)) {
                                $configurationClasses[] = $configurationClass;
                            }
                        }
                        break;
                }
            }
        }

        return $configurationClasses;
    }
}
