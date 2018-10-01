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
use Tvi\MonitorBundle\Check\php\PhpVersion\Check;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 * @coversNothing
 *
 * @internal
 */
class CheckTest extends TestCase
{
    /**
     * @var Check
     */
    protected $check;

    protected function setUp()
    {
        $this->check = new Check('7.0', '=');
        $this->check->setId('php_version');

        $this->check->setTags(['tag1', 'tag2']);
        $this->check->setGroup('group');
    }

    public function test_id()
    {
        $this->assertSame('php_version', $this->check->getId());
    }

    public function test_group()
    {
        $this->assertSame('group', $this->check->getGroup());
    }

    public function test_tags()
    {
        $this->assertSame(['tag1', 'tag2'], $this->check->getTags());
    }

    public function test_label()
    {
        $this->assertSame('Check', $this->check->getLabel());
    }

    public function test_addition_params()
    {
        $this->check->setAdditionParams(['id' => 'id', 'label' => 'test', 'tags' => ['tag'], 'group' => 'testGroup']);

        $this->assertSame('test', $this->check->getLabel());
        $this->assertSame(['tag'], $this->check->getTags());
        $this->assertSame('testGroup', $this->check->getGroup());
        $this->assertSame('id', $this->check->getId());
    }
}
