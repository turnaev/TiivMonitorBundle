<?php
/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\ClassExists;

use PHPUnit\Framework\TestCase;
use ZendDiagnostics\Result\FailureInterface;
use ZendDiagnostics\Result\ResultInterface;
use Tvi\MonitorBundle\Check\CheckInterface;
use ZendDiagnostics\Result\SuccessInterface;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Test extends TestCase
{
    /**
     * @var Check
     */
    protected $checker;

    public function setUp()
    {
        $this->checker = new Check(self::class);
    }

    public function testCheck()
    {
        $this->assertInstanceOf(CheckInterface::class, $this->checker);
        $this->assertInstanceOf(ResultInterface::class, $this->checker->check());
    }

    public function testCases()
    {
        $checker = new Check(self::class);
        $this->assertInstanceOf(SuccessInterface::class, $checker->check());

        $checker = new Check('note_exuist_class');
        $this->assertInstanceOf(FailureInterface::class, $checker->check());

        $checker = new Check(['note_exuist_class', self::class]);
        $this->assertInstanceOf(FailureInterface::class, $checker->check());
    }
}
