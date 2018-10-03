<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Test\Reporter;

use PHPUnit\Framework\TestCase;
use Tvi\MonitorBundle\Reporter\Console;
use Tvi\MonitorBundle\Reporter\Nagius;
use Tvi\MonitorBundle\Reporter\ReporterManager;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 *
 * @internal
 */
class ReporterManagerTest extends TestCase
{
    public function test_reportes()
    {
        $manager = new ReporterManager();
        $manager->addReporter('console', new Console(), 'a');
        $manager->addReporter('nagius', new Nagius(), 'b');

        $this->assertInstanceOf(Console::class, $manager->getReporter('console'));
        $this->assertInstanceOf(Console::class, $manager->getReporter('console', 'a'));

        $this->assertInstanceOf(Nagius::class, $manager->getReporter('nagius'));
        $this->assertInstanceOf(Nagius::class, $manager->getReporter('nagius', 'b'));

        $this->assertNull($manager->getReporter('nagius', 'not'));
        $this->assertNull($manager->getReporter('nagius_not', 'b'));
        $this->assertNull($manager->getReporter('nagius_not'));
    }
}
