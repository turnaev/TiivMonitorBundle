<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\php\ClassExists;

use Tvi\MonitorBundle\Check\CheckInterface;
use Tvi\MonitorBundle\Test\Check\CheckTestCase;
use ZendDiagnostics\Result\FailureInterface;
use ZendDiagnostics\Result\ResultInterface;
use ZendDiagnostics\Result\SuccessInterface;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 * @coversNothing
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
        $check = new Check(self::class);
        $this->assertInstanceOf(CheckInterface::class, $check);
        $this->assertInstanceOf(ResultInterface::class, $check->check());
    }

    public function test_cases()
    {
        $check = new Check(self::class);
        $this->assertInstanceOf(SuccessInterface::class, $check->check());

        $check = new Check('note_exuist_class');
        $this->assertInstanceOf(FailureInterface::class, $check->check());

        $check = new Check(['note_exuist_class', self::class]);
        $this->assertInstanceOf(FailureInterface::class, $check->check());
    }
}
