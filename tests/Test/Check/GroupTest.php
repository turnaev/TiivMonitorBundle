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
use Tvi\MonitorBundle\Test\Check\TestSuccessCheck\Check as TestSuccessCheck;
use Tvi\MonitorBundle\Check\Group;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 *
 * @internal
 */
class GroupTest extends TestCase
{
    /**
     * @var Group
     */
    protected $group;

    protected function setUp()
    {
        $this->group = new Group('test');

        $check = new TestSuccessCheck();
        $check->setId('test:success:check');

        $this->group->addCheck($check->getId(), $check);
    }

    public function test_id()
    {
        $this->assertSame('test', $this->group->getId());
    }

    public function test_name()
    {
        $this->assertSame('test', $this->group->getName());

        $this->group->setName('testNew');
        $this->assertSame('testNew', $this->group->getName());
    }

    public function test_descr()
    {
        $this->assertNull($this->group->getDescr());

        $this->group->setDescr('testNewDescr');
        $this->assertSame('testNewDescr', $this->group->getDescr());
    }

    public function test_get_check_ids()
    {
        $this->assertCount(1, $this->group->getCheckIds());
    }

    public function test_add_check()
    {
        $check = new TestSuccessCheck();
        $check->setId('test:success:check.b');

        $this->group->addCheck($check->getId(), $check);

        $this->assertCount(2, $this->group->getCheckIds());
    }

    public function test_get_label()
    {
        $this->assertSame('test(1)', $this->group->getLabel());

        $check = new TestSuccessCheck();
        $check->setId('test:success:check.b');

        $this->group->addCheck($check->getId(), $check);

        $this->assertSame('test(2)', $this->group->getLabel());
    }
}
