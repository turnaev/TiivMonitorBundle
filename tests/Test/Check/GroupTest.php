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
use Tvi\MonitorBundle\Check\Group;
use Tvi\MonitorBundle\Check\php\PhpVersion\Check;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 * @coversNothing
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
        $this->group = new Group('testGroup');

        $check = new Check('7.0', '=');
        $check->setId('php_version');
        $this->group->addCheck($check->getId(), $check);
    }

    public function test_get_name()
    {
        $this->assertSame('testGroup', $this->group->getName());
    }

    public function test_get_checknames()
    {
        $this->assertCount(1, $this->group->getChecknames());
    }

    public function test_add_check()
    {
        $check = new Check('7.0', '=');
        $check->setId('php_version.b');
        $this->group->addCheck($check->getId(), $check);

        $this->assertCount(2, $this->group->getChecknames());
    }

    public function test_get_label()
    {
        $this->assertSame('testGroup (1)', $this->group->getLabel());
    }
}
