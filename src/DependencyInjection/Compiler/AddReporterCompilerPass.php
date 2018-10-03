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
use Symfony\Component\DependencyInjection\Reference;
use Tvi\MonitorBundle\DependencyInjection\DiTags;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class AddReporterCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $reportersManagerDefinition = $container->getDefinition('tvi_monitor.reporters.manager');

        $reporterDefinitions = $container->findTaggedServiceIds(DiTags::CHECK_REPORTER);

        foreach ($reporterDefinitions as $id => $reporterDefinition) {
            $scope = $reporterDefinition[0]['scope'] ?? null;
            $alias = $reporterDefinition[0]['alias'];

            $reportersManagerDefinition->addMethodCall('addReporter', [$alias, new Reference($id), $scope]);
        }
    }
}
