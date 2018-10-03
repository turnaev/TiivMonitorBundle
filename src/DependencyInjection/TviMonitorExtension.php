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

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDOSqlite\Driver;
use Doctrine\DBAL\Migrations\MigrationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class TviMonitorExtension extends Extension implements CompilerPassInterface
{
    /**
     * Connection object needed for correct migration loading.
     *
     * @var Connection
     */
    private $fakeConnection;

    /**
     * Loads the services based on your application configuration.
     *
     * @throws MigrationException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function load(array $configs, ContainerBuilder $container)
    {
//        v($configs); exit;

        $this->fakeConnection = new Connection([], new Driver());
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('service.yml');
        $loader->load('command.yml');

        //$loader->load('controller.yml');
        //$loader->load('telega.yml');

        $checksSearchPaths = $configs[1]['checks_search_paths'] ?? [];
        unset($configs[1]['checks_search_paths']);

        $configuration = new Configuration($checksSearchPaths);
        $config = $this->processConfiguration($configuration, $configs);

//        v($config); exit;

        $this->configureTags($config, $container);
        $this->configureChecks($config, $container, $loader, $configuration->getCheckPlugins());

        $this->configureReporters($config, $container, $loader);
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
    }

    private function configureTags(array $config, ContainerBuilder $container)
    {
        $container->setParameter(sprintf('%s.tags', $this->getAlias()), $config['tags']);
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
                    $checkPlugin['pligin']->checkRequirements();
                }

                if (isset($checkSettings['items'])) {
                    $items = $checkSettings['items'];

                    foreach ($items as $itemName => &$item) {
                        $item['tags'] = array_unique(array_merge($item['tags'], $checkSettings['tags']));

                        if (null === $item['label'] && null !== $checkSettings['label']) {
                            $label = $checkSettings['label'];
                            $label = sprintf($label, $itemName);
                            $item['label'] = $label;
                        }
                    }

                    $containerParams[$service]['_multi'] = $items;
                } else {
                    $containerParams[$service]['_singl'] = $checkSettings;
                }
            }
        }

        $id = sprintf('%s.checks.conf', $this->getAlias());
        $container->setParameter($id, $containerParams);
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
