<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Test\Runner;

use Tvi\MonitorBundle\Runner\Runner;
use Tvi\MonitorBundle\Test\Base\ExtensionTestCase;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 * @coversNothing
 */
class ManagerTest extends ExtensionTestCase
{
    public function test_get_runner()
    {
        $this->load();
        $this->compile();

        $manager = $this->container->get('tvi_monitor.runner.manager');

        $runner = $manager->getRunner();

        $this->assertInstanceOf(Runner::class, $runner);
    }
}
