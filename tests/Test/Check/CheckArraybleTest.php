<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Test\Check;

use PHPUnit\Framework\TestCase;
use Tvi\MonitorBundle\Check\CheckInterface;
use Tvi\MonitorBundle\Check\Group;
use Tvi\MonitorBundle\Test\Check\TestSuccessCheck\Check as TestSuccessCheck;
use Tvi\MonitorBundle\Test\Check\TestFailureCheck\Check as TestFailureCheck;
use Tvi\MonitorBundle\Check\Proxy;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 *
 * @internal
 */
class CheckArraybleTest extends TestCase
{
    /**
     * @var Group
     */
    protected $group;

    protected function setUp()
    {
        $this->group = new Group('testGroup');

        $check1 = new TestSuccessCheck();
        $check1->setId('test:success:check');

        $this->group->addCheck($check1->getId(), $check1);

        $check2 = new Proxy(static function () {
            $check2 = new TestFailureCheck();
            $check2->setId('test:failure:check');

            return $check2;
        });

        $this->group->addCheck('test:failure:check', $check2);
    }

    public function test_count()
    {
        $this->assertCount(2, $this->group);
    }

    public function test_offset_exists()
    {
        $this->assertTrue(isset($this->group['test:success:check']));
        $this->assertFalse(isset($this->group['not']));
    }

    public function test_offset_get()
    {
        $this->assertInstanceOf(CheckInterface::class, $this->group['test:success:check']);
        $this->assertInstanceOf(CheckInterface::class, $this->group['test:failure:check']);
    }

    public function test_offset_set()
    {
        $this->assertCount(2, $this->group);

        $check = new TestSuccessCheck();
        $check->setId('test:failure:check2');

        $this->group['test:failure:check2'] = $check;

        $this->assertCount(3, $this->group);
    }

    public function test_offset_unset()
    {
        $this->assertCount(2, $this->group);
        unset($this->group['test:failure:check']);
        $this->assertCount(1, $this->group);
    }

    public function test_to_array()
    {
        $this->assertCount(2, $this->group->toArray());
    }

    public function test_traversable()
    {
        foreach ($this->group as $check) {
            $this->assertInstanceOf(CheckInterface::class, $check);
        }
    }
}
