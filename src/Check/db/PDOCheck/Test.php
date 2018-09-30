<?php
/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\db\PDOCheck;

use ZendDiagnostics\Result\ResultInterface;
use Tvi\MonitorBundle\Check\CheckInterface;
use Tvi\MonitorBundle\Test\Check\CheckTestCase;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Test extends CheckTestCase
{
    public function testIntegration()
    {
        $this->iterateConfTest(__DIR__ . '/config.example.yml');
    }

    public function testCheck()
    {
        $check = new Check('mysql:host=localhost;dbname=testdb', 'username', 'password');
        $this->assertInstanceOf(CheckInterface::class, $check);
        $this->assertInstanceOf(ResultInterface::class, $check->check());
    }
}
