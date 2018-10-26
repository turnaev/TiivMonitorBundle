<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Tvi\MonitorBundle\DependencyInjection\DiTags;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class AddCheckPluginsCompilerPass implements CompilerPassInterface
{
    const SERVICE_ID_FORMAT = 'tvi_monitor.check.%s';

    /**
     * @var array
     */
    protected $checkServiceConfs = [];

    public function process(ContainerBuilder $container)
    {
        $this->processChecks($container);
    }

    protected function processChecks(ContainerBuilder $container)
    {
        $checkConfigs = $container->getParameter('tvi_monitor.conf.checks');

        $container->setParameter('tvi_monitor.checks.conf', null);

        $checkServiceIds = $container->findTaggedServiceIds(DiTags::CHECK_PLUGIN);

        foreach ($checkServiceIds as $checkServiceId => $null) {
            $checkDefinitionTpl = $container->getDefinition($checkServiceId);

            $container->removeDefinition($checkServiceId);

            $checkTag = $checkDefinitionTpl->getTag(DiTags::CHECK_PLUGIN);
            $checkPluginAlias = $checkTag[0]['alias'];

            $checkConfig = $checkConfigs[$checkPluginAlias];


            if (isset($checkConfig['_singl'])) {
                $this->addCheckPlugin($container, $checkDefinitionTpl, $checkConfig['_singl'], $checkPluginAlias);
            }

            if (isset($checkConfig['_multi'])) {
                foreach ($checkConfig['_multi'] as $pref => $conf) {
                    $this->addCheckPlugin($container, $checkDefinitionTpl, $conf, $checkPluginAlias, $pref);
                }
            }
        }

        $registryDefinition = $container->getDefinition('tvi_monitor.checks.manager');

        $tagConfs = $container->getParameter('tvi_monitor.conf.tags');
        $container->setParameter('tvi_monitor.tags', null);

        $groupConfs = $container->getParameter('tvi_monitor.conf.groups');
        $container->setParameter('tvi_monitor.conf.groups', null);

        $registryDefinition->addMethodCall('add', [$this->checkServiceConfs, $tagConfs, $groupConfs]);
    }

    protected function addCheckPlugin(ContainerBuilder $container, Definition $checkPluginDefinitionTpl, array $conf, string $checkPluginAlias, string $checkPluginPref = null)
    {
        $checkPluginDefinition = clone $checkPluginDefinitionTpl;

        if (null !== $checkPluginPref) {
            $checkPluginAlias .= '.'.$checkPluginPref;
        }

        if (isset($conf['importance'])) {
            $conf['tags'][] = $conf['importance'];
        }

        foreach ($checkPluginDefinition->getArguments() as $argumentIndex => $argument) {
            $argument = str_replace('%%', '', $argument);

            if (array_key_exists($argument, $conf['check'])) {
                $checkPluginDefinition->replaceArgument($argumentIndex, $conf['check'][$argument]);
            }
        }

        $methodCalls = $checkPluginDefinition->getMethodCalls();

        foreach ($methodCalls as &$methodCall) {
            if ('setAdditionParams' === $methodCall[0]) {
                $conf['id'] = $checkPluginAlias;
                $methodCall[1][0] = $conf;
            }
        }

        $checkPluginDefinition->setMethodCalls($methodCalls);

        $checkServiceId = sprintf(self::SERVICE_ID_FORMAT, sha1($checkPluginAlias));

        $container->setDefinition($checkServiceId, $checkPluginDefinition);

        $this->checkServiceConfs[$checkPluginAlias] = ['serviceId' => $checkServiceId, 'group' => $conf['group'], 'tags' => $conf['tags']];
    }
}
