<?php

/*
 * This file is part of the Sonata Project package.
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Test\Runner;

use Tvi\MonitorBundle\Runner\Runner;
use Tvi\MonitorBundle\Test\Base\ExtensionTestCase;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class ManagerTest extends ExtensionTestCase
{
    public function testGetRunner()
    {
        $this->load();
        $this->compile();

        $manager = $this->container->get('tvi_monitor.runner.manager');

        $runner = $manager->getRunner();

        $this->assertInstanceOf(Runner::class, $runner);
    }
}
