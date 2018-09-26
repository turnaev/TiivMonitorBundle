<?php
/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\PhpVersion;

use PHPUnit\Framework\TestCase;
use Tvi\MonitorBundle\Check\CheckInterface;
use ZendDiagnostics\Result\ResultInterface;

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
        $this->checker = new Check('7.0');
    }

    public function testCheck()
    {
        $this->assertInstanceOf(CheckInterface::class, $this->checker);
        $this->assertInstanceOf(ResultInterface::class, $this->checker->check());
    }
}
