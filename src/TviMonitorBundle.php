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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tvi\MonitorBundle\DependencyInjection\Compiler\AddChecksCompilerPass;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class TviMonitorBundle extends Bundle
{
    public function boot()
    {
        parent::boot();
        # HHVM compatibility hack
        if (defined('HHVM_VERSION')) {

            if (!\function_exists('error_clear_last')) {
                function error_clear_last()
                {
                    \set_error_handler(function () {});
                    try {
                        \trigger_error('');
                    } catch (\Exception $e) {
                        \restore_error_handler();
                        throw $e;
                    }
                    \restore_error_handler();
                }
            }
        }
    }

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddChecksCompilerPass());
    }
}
