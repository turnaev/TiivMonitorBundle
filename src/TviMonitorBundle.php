<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle;

use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tvi\MonitorBundle\DependencyInjection\Compiler\AddCheckPluginsCompilerPass;
use Tvi\MonitorBundle\DependencyInjection\Compiler\AddReporterCompilerPass;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class TviMonitorBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container
            ->addCompilerPass(new AddCheckPluginsCompilerPass())
            ->addCompilerPass(new AddReporterCompilerPass());
    }
}
