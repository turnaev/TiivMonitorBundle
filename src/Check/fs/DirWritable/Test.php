<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\fs\DirWritable;

use Tvi\MonitorBundle\Check\CheckInterface;
use Tvi\MonitorBundle\Test\Check\CheckTestCase;
use ZendDiagnostics\Result\ResultInterface;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 *
 * @internal
 */
class Test extends CheckTestCase
{
    public function test_integration()
    {
        $this->iterateConfTest(__DIR__.'/config.example.yml');
    }

    public function test_check()
    {
        $check = new Check('/tmp');
        $this->assertInstanceOf(CheckInterface::class, $check);
        $this->assertInstanceOf(ResultInterface::class, $check->check());
    }
}
