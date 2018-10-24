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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ConfigurationExtensionInterface;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class TviMonitorExtension extends Extension
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * Loads the services based on your application configuration.
     */
    public function load(array $configs, ContainerBuilder $container)
    {

        /*
        dump($configs);
        exit;
        //*/

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('service.yml');
        $loader->load('command.yml');
        $loader->load('controller.yml');
        $loader->load('generator.yml');

        $this->configurePluginFinder($configs, $container);
        $pluginFinder = $container->get('tvi_monitor.checks.plugin_finder');

        $this->configuration = new Configuration($pluginFinder);
        $config = $this->processConfiguration($this->configuration, $configs);

        /*
        dump($config);
        exit;
        //*/

        $this->configureUIViewTemplate($config, $container);
        $this->configureTags($config, $container);
        $this->configureGroups($config, $container);
        $this->configureReporters($config, $container, $loader);

        $this->configureChecks($config, $container, $loader, $this->configuration->getCheckPlugins());


    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return $this->configuration;
    }

    private function configurePluginFinder(array $configs, ContainerBuilder $container)
    {
        $checksSearchPaths = [];
        if (isset($configs[1]['checks_search_paths'])) {
            $checksSearchPaths = $configs[1]['checks_search_paths'] ?? [];
        } elseif (isset($configs[0]['checks_search_paths'])) {
            $checksSearchPaths = $configs[0]['checks_search_paths'] ?? [];
        }

        $checkPluginFinderDefinition = $container->getDefinition('tvi_monitor.checks.plugin_finder');
        $checkPluginFinderDefinition->setArguments([$checksSearchPaths]);
        $services = $container->findTaggedServiceIds(DiTags::CHECK_PLUGIN_SEARCH_PATH);
v($services);
        foreach ($services as $id => $null) {
            $service = new \Symfony\Component\DependencyInjection\Reference($id);
            $checkPluginFinderDefinition->addMethodCall('addCheckPluginFinderPath', [$service]);
        }
    }

    private function configureUIViewTemplate(array $config, ContainerBuilder $container)
    {
        $container->setParameter(sprintf('%s.ui.view.template', $this->getAlias()), $config['ui_view_template']);
    }

    private function configureTags(array $config, ContainerBuilder $container)
    {
        $container->setParameter(sprintf('%s.conf.tags', $this->getAlias()), $config['tags']);
    }

    private function configureGroups(array $config, ContainerBuilder $container)
    {
        $container->setParameter(sprintf('%s.conf.groups', $this->getAlias()), $config['groups']);
    }

    /**
     * @param string[] $checkPlugins
     *
     * @throws \Exception
     */
    private function configureChecks(array $config, ContainerBuilder $container, YamlFileLoader $loader, array $checkPlugins)
    {
        $containerParams = [];

        if (isset($config['checks'])) {
            $config['checks'] = array_filter($config['checks'], static function ($i) {
                return $i;
            });

            $containerParams = [];
            $checksLoaded = [];

            foreach ($config['checks'] as $checkName => &$checkSettings) {
                $checkPlugin = $checkPlugins[$checkName];
                $service = $checkPlugin['service'];

                $checkServicePath = $checkPlugin['checkServicePath'];

                if (!\in_array($checkServicePath, $checksLoaded, true)) {
                    $checksLoaded[] = $checkServicePath;

                    $loader->load($checkServicePath);
                    $checkPlugin['plugin']->checkRequirements($checkSettings);
                }

                if (isset($checkSettings['items'])) {
                    $items = $checkSettings['items'];

                    foreach ($items as $itemName => &$item) {
                        $item['tags'] = array_unique(array_merge($item['tags'], $checkSettings['tags']));

                        if (null === $item['label'] && null !== $checkSettings['label']) {
                            $val = $checkSettings['label'];
                            $val = sprintf($val, $itemName);
                            $item['label'] = $val;
                        }

                        if (null === $item['descr'] && null !== $checkSettings['descr']) {
                            $val = $checkSettings['descr'];
                            $val = sprintf($val, $itemName);
                            $item['descr'] = $val;
                        }

                        if (empty($item['group']) && !empty($checkSettings['group'])) {
                            $val = $checkSettings['group'];
                            $item['group'] = $val;
                        }

                        if (empty($item['importance']) && !empty($checkSettings['importance'])) {
                            $val = $checkSettings['importance'];
                            $item['importance'] = $val;
                        }

                        if (empty($item['group']) && empty($checkSettings['group'])) {
                            $item['group'] = $item['_group'];
                        }

                        unset($item['_group']);
                    }

                    $containerParams[$service]['_multi'] = $items;
                } else {
                    if (empty($checkSettings['group'])) {
                        $checkSettings['group'] = $checkSettings['_group'];
                    }

                    unset($checkSettings['_group']);
                    $containerParams[$service]['_singl'] = $checkSettings;
                }
            }
        }

        $container->setParameter(sprintf('%s.conf.checks', $this->getAlias()), $containerParams);
    }

    /**
     * @throws \Exception
     */
    private function configureReporters(array $config, ContainerBuilder $container, YamlFileLoader $loader)
    {
        $loader->load('reporter/reporters.yml');

        if (isset($config['reporters']['mailer'])) {
            $loader->load('reporter/mailer.yml');

            foreach ($config['reporters']['mailer'] as $key => $value) {
                $container->setParameter(sprintf('%s.mailer.%s', $this->getAlias(), $key), $value);
            }
        }
    }
}
