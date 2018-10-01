<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\fs\YamlFile;

use Tvi\MonitorBundle\Check\CheckInterface;
use Tvi\MonitorBundle\Test\Check\CheckTestCase;
use ZendDiagnostics\Result\ResultInterface;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Test extends CheckTestCase
{
    public function testIntegration()
    {
        $this->iterateConfTest(__DIR__.'/config.example.yml');
    }

    public function testCheck()
    {
        $check1 = new Check('/tmp/test.yml'  );

        $this->assertInstanceOf(CheckInterface::class, $check1);
        $this->assertInstanceOf(ResultInterface::class, $check1->check());
    }
}
