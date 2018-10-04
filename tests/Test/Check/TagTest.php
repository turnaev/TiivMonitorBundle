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
        $this->tag = new Tag('testTag');

        $check = new TestSuccessCheck();
        $check->setId('test:success:check');
        $this->tag->addCheck($check->getId(), $check);
    }

    public function test_get_name()
    {
        $this->assertSame('testTag', $this->tag->getName());
    }

    public function test_get_checknames()
    {
        $this->assertCount(1, $this->tag->getChecknames());
    }

    public function test_add_check()
    {
        $check = new TestSuccessCheck();
        $check->setId('test:success:check.b');
        $this->tag->addCheck($check->getId(), $check);

        $this->assertCount(2, $this->tag->getChecknames());
    }

    public function test_get_label()
    {
        $this->assertSame('testTag', $this->tag->getLabel());
    }
}
