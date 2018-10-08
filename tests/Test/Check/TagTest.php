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
use Tvi\MonitorBundle\Check\Tag;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 *
 * @internal
 */
class TagTest extends TestCase
{
    /**
     * @var Tag
     */
    protected $tag;

    protected function setUp()
    {
        $this->tag = new Tag('test');

        $check = new TestSuccessCheck();
        $check->setId('test:success:check');

        $this->tag->addCheck($check->getId(), $check);
    }

    public function test_id()
    {
        $this->assertSame('test', $this->tag->getId());
    }

    public function test_name()
    {
        $this->assertSame('test', $this->tag->getName());

        $this->tag->setName('testNew');
        $this->assertSame('testNew', $this->tag->getName());
    }

    public function test_descr()
    {
        $this->assertNull($this->tag->getDescr());

        $this->tag->setDescr('testNewDescr');
        $this->assertSame('testNewDescr', $this->tag->getDescr());
    }

    public function test_get_check_ids()
    {
        $this->assertCount(1, $this->tag->getCheckIds());
    }

    public function test_add_check()
    {
        $check = new TestSuccessCheck();
        $check->setId('test:success:check.b');

        $this->tag->addCheck($check->getId(), $check);

        $this->assertCount(2, $this->tag->getCheckIds());
    }

    public function test_get_label()
    {
        $this->assertSame('test(1)', $this->tag->getLabel());

        $check = new TestSuccessCheck();
        $check->setId('test:success:check.b');

        $this->tag->addCheck($check->getId(), $check);

        $this->assertSame('test(2)', $this->tag->getLabel());
    }
}
