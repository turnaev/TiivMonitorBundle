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
     * @param array            $configs
     * @param ContainerBuilder $container
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
        $this->configureChecks($config, $container, $loader, $configuration->getCheckMatadatas());

        $this->configureReportersMailer($config, $container, $loader);
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
     * @param array            $config
     * @param ContainerBuilder $container
     * @param YamlFileLoader   $loader
     * @param string[]         $checkMatadatas
     *
     * @throws \Exception
     */
    private function configureChecks(array $config, ContainerBuilder $container, YamlFileLoader $loader, array $checkMatadatas)
    {
        $containerParams = [];

        if (isset($config['checks'])) {
            $config['checks'] = array_filter($config['checks'], function ($i) {
                return $i;
            });

            $containerParams = [];
            $checksLoaded = [];
            foreach ($config['checks'] as $checkName => &$checkSettings) {
                $this->checkRequirement($checkName);

                $checkMatadata = $checkMatadatas[$checkName];
                $service = $checkMatadata['service'];

                $path = $checkMatadata['path'].\DIRECTORY_SEPARATOR.$checkMatadata['conf'];

                if (!\in_array($path, $checksLoaded, true)) {
                    $loader->load($path);
                    $checksLoaded[] = $path;
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
     * @param ContainerBuilder $container
     * @param string           $checkName
     * @param array            $settings
     */
    private function checkRequirement($checkName)
    {
        switch ($checkName) {

            case 'symfony_version':
                continue;

            case 'opcache_memory':
                if (!class_exists('ZendDiagnostics\Check\OpCacheMemory')) {
                    throw new \InvalidArgumentException('Please require at least "v1.0.4" of "ZendDiagnostics"');
                }
                continue;

            case 'doctrine_migration':
                if (!class_exists('ZendDiagnostics\Check\DoctrineMigration')) {
                    throw new \InvalidArgumentException('Please require at least "v1.0.6" of "ZendDiagnostics"');
                }

                if (!class_exists('Doctrine\DBAL\Migrations\Configuration\Configuration')) {
                    throw new \InvalidArgumentException('Please require at least "v1.1.0" of "DB Migrations Library"');
                }
                continue;

            case 'pdo_connection':
                if (!class_exists('ZendDiagnostics\Check\PDOCheck')) {
                    throw new \InvalidArgumentException('Please require at least "v1.0.5" of "ZendDiagnostics"');
                }
                continue;

            default:
        }
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     * @param YamlFileLoader   $loader
     *
     * @throws \Exception
     */
    private function configureReportersMailer(array $config, ContainerBuilder $container, YamlFileLoader $loader)
    {
        if (isset($config['reporters']['mailer'])) {
            $loader->load('swift_mailer.yml');

            foreach ($config['reporters']['mailer'] as $key => $value) {
                $container->setParameter(sprintf('%s.mailer.%s', $this->getAlias(), $key), $value);
            }
        }
    }
}
