<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\php\Expression;

use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\ResultInterface;
use ZendDiagnostics\Result\Success;
use ZendDiagnostics\Result\Warning;
use Tvi\MonitorBundle\Test\Check\CheckTestCase;

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

    /**+
     * @dataProvider checkResultProvider
     */
    public function test_check($warningExpression, $criticalExpression, $warningMessage, $criticalMessage, $expectedResultClass, $expectedMessage)
    {
        $check = new Check($warningExpression, $criticalExpression, $warningMessage, $criticalMessage);

        $result = $check->check();

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertInstanceOf($expectedResultClass, $result);
        $this->assertSame($expectedMessage, $result->getMessage());
    }

    public function test_check_bash()
    {
        $this->expectException(\InvalidArgumentException::class);

        new Check();
    }

    public function checkResultProvider()
    {
        return [
            ['true', 'true', null, null, Success::class, ''],
            ['false', 'true', 'warning', 'fail', Warning::class, 'warning'],
            ['true', 'false', 'warning', 'fail', Failure::class, 'fail'],
            ['false', 'false', 'warning', 'fail', Failure::class, 'fail'],
        ];
    }
}
