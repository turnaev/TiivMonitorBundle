<?php
/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Test\Base;

use Tvi\MonitorBundle\DependencyInjection\Compiler\AddChecksCompilerPass;
use Tvi\MonitorBundle\DependencyInjection\TviMonitorExtension;
use Tvi\MonitorBundle\TviMonitorBundle;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
abstract class ExtensionTestCase extends \Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase
{
    protected function getContainerExtensions()
    {
        return [new TviMonitorExtension()];
    }

    protected function compile()
    {
        $this->container->addCompilerPass(new AddChecksCompilerPass());

        parent::compile();
    }
}
